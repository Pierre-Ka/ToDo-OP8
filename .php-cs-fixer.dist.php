<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;

/**************************	PhpCsFixer : Fix code **********

		Via la commande : Voir seulement
			- vendor/bin/php-cs-fixer fix src -vvv --dry-run --show-progress=dots
		Via la commande : Jouer la correction
			- vendor/bin/php-cs-fixer fix src -vvv --show-progress=dots
			
/******************************	Rule set @Symfony¶ **********
Rules that follow the official Symfony Coding Standards.
Rules¶ : 
	@PSR12
	array_syntax
	backtick_to_shell_exec
	binary_operator_spaces
	blank_line_before_statement config: ['statements' => ['return']]
	braces config: ['allow_single_line_anonymous_class_with_empty_body' => true, 'allow_single_line_closure' => true]
	cast_spaces
	class_attributes_separation config: ['elements' => ['method' => 'one']]
	class_definition config: ['single_line' => true]
	class_reference_name_casing
	clean_namespace
	concat_space
	echo_tag_syntax
	empty_loop_body config: ['style' => 'braces']
	empty_loop_condition
	fully_qualified_strict_types
	function_typehint_space
	general_phpdoc_tag_rename config: ['replacements' => ['inheritDocs' => 'inheritDoc']]
	include
	increment_style
	integer_literal_case
	lambda_not_used_import
	linebreak_after_opening_tag
	magic_constant_casing
	magic_method_casing
	method_argument_space config: ['on_multiline' => 'ignore']
	native_function_casing
	native_function_type_declaration_casing
	no_alias_language_construct_call
	no_alternative_syntax
	no_binary_string
	no_blank_lines_after_phpdoc
	no_empty_comment
	no_empty_phpdoc
	no_empty_statement
	no_extra_blank_lines config: ['tokens' => ['case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'switch', 'throw', 'use']]
	no_leading_namespace_whitespace
	no_mixed_echo_print
	no_multiline_whitespace_around_double_arrow
	no_short_bool_cast
	no_singleline_whitespace_before_semicolons
	no_spaces_around_offset
	no_superfluous_phpdoc_tags config: ['allow_mixed' => true, 'allow_unused_params' => true]
	no_trailing_comma_in_list_call
	no_trailing_comma_in_singleline_array
	no_trailing_comma_in_singleline_function_call
	no_unneeded_control_parentheses config: ['statements' => ['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield', 'yield_from']]
	no_unneeded_curly_braces config: ['namespaces' => true]
	no_unneeded_import_alias
	no_unset_cast
	no_unused_imports
	no_whitespace_before_comma_in_array
	normalize_index_brace
	object_operator_without_whitespace
	ordered_imports
	php_unit_fqcn_annotation
	php_unit_method_casing
	phpdoc_align
	phpdoc_annotation_without_dot
	phpdoc_indent
	phpdoc_inline_tag_normalizer
	phpdoc_no_access
	phpdoc_no_alias_tag
	phpdoc_no_package
	phpdoc_no_useless_inheritdoc
	phpdoc_return_self_reference
	phpdoc_scalar
	phpdoc_separation
	phpdoc_single_line_var_spacing
	phpdoc_summary
	phpdoc_tag_type config: ['tags' => ['inheritDoc' => 'inline']]
	phpdoc_to_comment
	phpdoc_trim
	phpdoc_trim_consecutive_blank_line_separation
	phpdoc_types
	phpdoc_types_order config: ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none']
	phpdoc_var_without_name
	protected_to_private
	semicolon_after_instruction
	single_class_element_per_statement
	single_line_comment_spacing
	single_line_comment_style config: ['comment_types' => ['hash']]
	single_line_throw
	single_quote
	single_space_after_construct
	space_after_semicolon config: ['remove_in_empty_for_expressions' => true]
	standardize_increment
	standardize_not_equals
	switch_continue_to_break
	trailing_comma_in_multiline
	trim_array_spaces
	types_spaces
	unary_operator_spaces
	whitespace_after_comma_in_array
	yoda_style
***********************************************************************/