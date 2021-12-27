<?php

namespace Application\Interfaces;

/**
 * @method void union($next_object, bool $all = FALSE, string $order = '', ?int $limit = NULL, ?int $offset = NULL, ?string $group_by = NULL)
 * @method void union_iterated($next_object, bool $all = FALSE, string $order = '', ?int $limit = NULL, ?int $offset = NULL, ?string $group_by = NULL)
 * @method string union_order_by_constant(string $column, string $direction = 'asc', ?string $lang_idiom = NULL, string $constant_prefix = 'user_custom_')
 * @method string union_order_by_overlay(string $column, string $target_table, string $target_column, string $target_table_id_field, string $direction = 'asc', ?string $lang_idiom = NULL)
 */
interface UnionsInterface
{
}