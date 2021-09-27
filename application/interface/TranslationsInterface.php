<?php

namespace Application\Interfaces;

/**
 * @method DataMapper order_by_with_constant(string $column, string $direction = 'asc', ?string $lang_idiom = NULL, string $constant_prefix = 'user_custom_')
 * @method DataMapper order_by_with_overlay(string $column, string $direction = 'asc', ?string $lang_idiom = NULL)
 * @method DataMapper order_by_related_with_constant(string $related, string $column, string $direction = 'asc', ?string $lang_idiom = NULL, string $constant_prefix = 'user_custom_')
 * @method DataMapper order_by_related_with_overlay(string $related, string $column, string $direction = 'asc', ?string $lang_idiom = NULL)
 * @method DataMapper like_with_constant(string $column, string $value, string $wrap = 'both', bool $strip_html = FALSE, ?string $lang_idiom = NULL, string $constant_prefix = 'user_custom_')
 * @method DataMapper or_like_with_constant(string $column, string $value, string $wrap = 'both', bool $strip_html = FALSE, ?string $lang_idiom = NULL, string $constant_prefix = 'user_custom_')
 * @method DataMapper like_with_overlay(string $column, string $value, string $wrap = 'both', bool $strip_html = FALSE, ?string $lang_idiom = NULL)
 * @method DataMapper or_like_with_overlay(string $column, string $value, string $wrap = 'both', bool $strip_html = FALSE, ?string $lang_idiom = NULL)
 */
interface TranslationsInterface
{
}