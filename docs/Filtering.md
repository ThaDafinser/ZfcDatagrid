# Filtering the grid

Every grid comes per default with a column filtering.

## Currently available filters
```sh
Note: the entered filter is trimmed automatically (left/right and between the operator/value)`
```

| WHERE condition   | Possible input value  | Description       |
| -------------     |-------------          | -----         |
| LIKE %value%      | `value` `~value` `%value%` `*value*` `~%value%` `~*value*` | **DEFAULT** like with wildcard left/right |
| LIKE value%       | `value%` `value*` `~value%` `~value*` | like with wildcard right |
| LIKE %value       | `%value` `*value` `~%value` `~*value` | like with wildcard left |
| NOT LIKE %value%  | `!~value` `!~%value%` `!~*value*``    | not like with wildcard left/right |
| NOT LIKE value%   | `!~value%` `!~value*`                 | not like with wildcard right |
| NOT LIKE %value   | `!~%value` `!~*value`                 | not like with wildcard left |
| = value           | `== value` `=value`                   | equal |
| != value          | `== value` `=value`                   | not equal |
| >= value          | `>=value`                             | greater equal |
| > value           | `>value`                              | greater |
| <= value          | `<=value`                             | less equal |
| < value           | `<value`                              | less |
| BETWEEN value1 AND value2  | `value1 <> value2`           | between two values |
| IN (value1, value2, ...) | `=(1,2)`                       | value inside the given values |
| NOT IN (value1, value2, ...)  | `!=(1,2,3)                | value not inside the given values |
