# Columns

Minimal column
```php
$col = new Column\Select('displayName');
$col->setLabel('Displayname');
$grid->addColumn($col);
```

Advanced
```php
$col = new Column\Select('displayName', 'tableNameOrAlias');
$col->setLabel('Displayname');
$col->setWidth(25);
$col->setSortDefault(1, 'ASC');
$col->addStyle(new Style\Bold());
$grid->addColumn($col);
```
