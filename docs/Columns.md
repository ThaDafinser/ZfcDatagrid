# Columns

Minimal column
```php
$col = new Column\Standard('displayName');
$col->setLabel('Displayname');
$dataGrid->addColumn($col);
```

Advanced
```php
$col = new Column\Standard('displayName', 'tableNameOrAlias');
$col->setLabel('Displayname');
$col->setWidth(25);
$col->setSortDefault(1, 'ASC');
$col->addStyle(new Style\Bold());
$dataGrid->addColumn($col);
```
