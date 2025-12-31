<?php
namespace Core;

/**
 * Model - Clase base para modelos (Active Record simple)
 */
abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [];
    protected static bool $timestamps = true;
    protected static bool $softDeletes = false;
    protected static bool $useTenant = true;

    protected array $attributes = [];
    protected array $original = [];

    /**
     * Obtiene la conexión a base de datos
     */
    protected static function db(): Database
    {
        return Database::getInstance();
    }

    /**
     * Obtiene el tenant_id actual
     */
    protected static function getTenantId(): ?int
    {
        return $_SESSION['tenant_id'] ?? null;
    }

    /**
     * Encuentra un registro por ID
     */
    public static function find(int $id): ?self
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        $tenantCond = static::tenantCondition();
        
        $sql = "SELECT * FROM {$table} WHERE {$pk} = ? AND {$tenantCond}";
        
        if (static::$softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }

        $data = static::db()->fetch($sql, [$id]);

        if (!$data) {
            return null;
        }

        return static::hydrate($data);
    }

    /**
     * Obtiene todos los registros
     */
    public static function all(array $columns = ['*']): array
    {
        $table = static::$table;
        $cols = implode(', ', $columns);
        $tenantCond = static::tenantCondition();
        
        $sql = "SELECT {$cols} FROM {$table} WHERE {$tenantCond}";
        
        if (static::$softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }

        $sql .= " ORDER BY " . static::$primaryKey . " DESC";

        $results = static::db()->fetchAll($sql);
        
        return array_map(fn($data) => static::hydrate($data), $results);
    }

    /**
     * Consulta con condiciones
     */
    public static function where(string $column, $value, string $operator = '='): QueryBuilder
    {
        return (new QueryBuilder(static::class))->where($column, $value, $operator);
    }

    /**
     * Inicia una nueva consulta
     */
    public static function query(): QueryBuilder
    {
        return new QueryBuilder(static::class);
    }

    /**
     * Obtiene el nombre de la tabla
     */
    public static function getTable(): string
    {
        return static::$table;
    }

    /**
     * Verifica si el modelo usa soft deletes
     */
    public static function hasSoftDeletes(): bool
    {
        return static::$softDeletes;
    }

    /**
     * Obtiene la condición de tenant
     */
    public static function tenantCondition(): string
    {
        if (!static::$useTenant) {
            return '1=1';
        }
        $tenantId = static::getTenantId();
        return $tenantId ? "tenant_id = {$tenantId}" : '1=1';
    }

    /**
     * Crea un nuevo registro
     */
    public static function create(array $data): self
    {
        $table = static::$table;
        
        // Filtrar solo campos permitidos
        $data = array_intersect_key($data, array_flip(static::$fillable));
        
        // Agregar tenant_id si aplica
        if (static::$useTenant && !isset($data['tenant_id'])) {
            $data['tenant_id'] = static::getTenantId();
        }
        
        // Agregar timestamps
        if (static::$timestamps) {
            $now = date('Y-m-d H:i:s');
            $data['created_at'] = $now;
            $data['updated_at'] = $now;
        }

        $id = self::db()->insert($table, $data);
        
        return static::find($id);
    }

    /**
     * Actualiza el registro actual
     */
    public function save(): bool
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        $id = $this->attributes[$pk] ?? null;

        if (!$id) {
            return false;
        }

        $data = array_intersect_key($this->attributes, array_flip(static::$fillable));
        
        if (static::$timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        self::db()->update($table, $data, "{$pk} = ?", [$id]);
        
        return true;
    }

    /**
     * Elimina el registro
     */
    public function delete(): bool
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        $id = $this->attributes[$pk] ?? null;

        if (!$id) {
            return false;
        }

        if (static::$softDeletes) {
            self::db()->update($table, ['deleted_at' => date('Y-m-d H:i:s')], "{$pk} = ?", [$id]);
        } else {
            self::db()->delete($table, "{$pk} = ?", [$id]);
        }

        return true;
    }

    /**
     * Cuenta registros
     */
    public static function count(string $where = '1=1', array $params = []): int
    {
        $table = static::$table;
        $tenantCond = static::tenantCondition();
        
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$tenantCond} AND ({$where})";
        
        if (static::$softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }

        $result = self::db()->fetch($sql, $params);
        return (int) $result['count'];
    }

    /**
     * Hidrata un modelo con datos
     */
    public static function hydrate(array $data): self
    {
        $instance = new static();
        $instance->attributes = $data;
        $instance->original = $data;
        return $instance;
    }

    /**
     * Acceso a atributos
     */
    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Establece atributos
     */
    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Verifica si existe un atributo
     */
    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Convierte a array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}

/**
 * Query Builder simple
 */
class QueryBuilder
{
    private string $modelClass;
    private array $wheres = [];
    private array $params = [];
    private ?string $orderBy = null;
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function where(string $column, $value, string $operator = '='): self
    {
        $this->wheres[] = "{$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy = "{$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function get(array $columns = ['*']): array
    {
        $model = $this->modelClass;
        $table = $model::getTable();
        $cols = implode(', ', $columns);
        
        $tenantCond = $model::tenantCondition();
        $whereCond = implode(' AND ', $this->wheres) ?: '1=1';
        
        $sql = "SELECT {$cols} FROM {$table} WHERE {$tenantCond} AND ({$whereCond})";
        
        if ($model::hasSoftDeletes()) {
            $sql .= " AND deleted_at IS NULL";
        }
        
        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy}";
        }
        
        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
            if ($this->offset) {
                $sql .= " OFFSET {$this->offset}";
            }
        }

        $results = Database::getInstance()->fetchAll($sql, $this->params);
        
        return array_map(fn($data) => $model::hydrate($data), $results);
    }

    public function first(): ?object
    {
        $results = $this->limit(1)->get();
        return $results[0] ?? null;
    }

    public function all(): array
    {
        return $this->get();
    }

    public function count(): int
    {
        $model = $this->modelClass;
        $table = $model::getTable();
        
        $tenantCond = $model::tenantCondition();
        $whereCond = implode(' AND ', $this->wheres) ?: '1=1';
        
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$tenantCond} AND ({$whereCond})";
        
        if ($model::hasSoftDeletes()) {
            $sql .= " AND deleted_at IS NULL";
        }

        $result = Database::getInstance()->fetch($sql, $this->params);
        return (int) $result['count'];
    }
}
