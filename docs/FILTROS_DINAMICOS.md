# Sistema de filtros dinámicos

## Estructura de un filtro

Cada filtro se representa como:

```json
{
  "field": "ciudad",
  "operator": "equals",
  "value": "Aguascalientes"
}
```

En el request (GET o POST) se envían como array:

- `filter_logic`: `"and"` | `"or"` (combinación entre filtros)
- `filters[0][field]`, `filters[0][operator]`, `filters[0][value]`
- `filters[1][field]`, ...

## Operadores

| Operador       | Descripción     | Uso típico   |
|----------------|-----------------|--------------|
| contains       | Contiene        | Texto        |
| not_contains   | No contiene     | Texto        |
| starts_with    | Empieza con     | Texto        |
| ends_with      | Termina con     | Texto        |
| equals         | Igual a         | Cualquiera   |
| not_equals     | Diferente de   | Cualquiera   |
| is_empty       | Está vacío      | Existencia   |
| is_not_empty   | No está vacío   | Existencia   |
| has_value      | Tiene valor     | Tel/Cel/Email|
| no_value       | No tiene valor  | Tel/Cel/Email|

## Campos por entidad

**Contactos:** genero, nombre_completo, telefono, celular, email, departamento, puesto_de_trabajo, municipio, estado, notas, domicilio, email_activo.

**Empresas:** sector (giro), municipio (ciudad), estado, datos_fiscales (domicilio), nombre_comercial (comercial).

## Uso en código

### Aplicar filtros a una query de contactos

```php
use App\Services\DynamicFilterService;
use App\DataTransferObjects\FilterSpec;

$filterService = app(DynamicFilterService::class);
$filters = [
    FilterSpec::fromArray(['field' => 'genero', 'operator' => 'equals', 'value' => 'Femenino']),
    FilterSpec::fromArray(['field' => 'municipio', 'operator' => 'contains', 'value' => 'Aguascalientes']),
];
$query = Contact::query();
$filterService->applyToContactQuery($query, $filters, 'and');
$contacts = $query->get();
```

### Parsear filtros desde el request

```php
$filterSpecs = DynamicFilterService::parseFromRequest($request);
$filterLogic = DynamicFilterService::logicFromRequest($request);
```

### Guardar una vista (SavedFilter)

```php
use App\Models\SavedFilter;

SavedFilter::create([
    'user_id' => auth()->id(),
    'name' => 'Mujeres en Aguascalientes',
    'entity' => 'contact',
    'filter_logic' => 'and',
    'filters' => [
        ['field' => 'genero', 'operator' => 'equals', 'value' => 'Femenino'],
        ['field' => 'municipio', 'operator' => 'contains', 'value' => 'Aguascalientes'],
    ],
]);
```

## Vistas donde aparecen los filtros

1. **Vista de Filtros** (`/filtros`): pestañas Contactos y Empresas, constructor dinámico + chips + resultados.
2. **Vista de Contactos** (`/contacts`): filtros rápidos + bloque “Filtros avanzados” (campo, operador, valor) y chips.
3. **Vista de Empresas** (`/companies`): igual que contactos, con campos de empresa.

Para agregar un nuevo campo filtrable, editar `FilterConfig::contactFields()` o `FilterConfig::companyFields()` y, si aplica, la lógica en `DynamicFilterService::applyContactFilter` / `applyCompanyFilter`.
