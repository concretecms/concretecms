<?php

namespace Concrete\Core\Support\CodingStyle;

use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;
use PhpCsFixer\WhitespacesFixerConfig;

class PhpFixerRuleResolver
{
    /**
     * @var \PhpCsFixer\FixerFactory
     */
    private $fixerFactory;

    /**
     * @param \PhpCsFixer\FixerFactory $fixerFactory
     */
    public function __construct(FixerFactory $fixerFactory)
    {
        $this->fixerFactory = $fixerFactory;
    }

    /**
     * Get a description of the specified flags.
     *
     * @param int $flags A combination of CodingStyle::FLAG_... flags.
     *
     * @return string
     */
    public function describeFlags($flags)
    {
        $flags = (int) $flags;
        if (($flags & PhpFixer::FLAG_PSR4CLASS) === PhpFixer::FLAG_PSR4CLASS) {
            $result = 'PHP-only files with the same name as the class it implements';
        } elseif (($flags & PhpFixer::FLAG_BOOTSTRAP) === PhpFixer::FLAG_BOOTSTRAP) {
            $result = 'PHP-only files with syntax compatible with old PHP versions';
        } elseif ($flags === 0) {
            $result = 'files with mixed contents';
        } else {
            $words = [];
            if (($flags & PhpFixer::FLAG_OLDPHP) === PhpFixer::FLAG_OLDPHP) {
                $words[] = 'files with syntax compatible with old PHP versions';
            }
            if (($flags & PhpFixer::FLAG_PHPONLY) === PhpFixer::FLAG_PHPONLY) {
                $words[] = 'PHP-only files';
            }
            $result = implode(', ', $words);
        }

        return $result;
    }

    /**
     * Get the rules associated to specific flags.
     *
     * @param int $flags A combination of PhpFixer::FLAG_... flags.
     * @param bool $onlyAvailableOnes return only the available rules
     * @param string $minimumPhpVersion the minimum PHP version that the files to be checked/fixed must be compatible with
     *
     * @return array
     */
    public function getRules($flags, $onlyAvailableOnes, $minimumPhpVersion = '')
    {
        $flags = (int) $flags;

        $hasFlag = function ($flag) use ($flags) {
            return ($flags & $flag) === $flag;
        };

        // Invariant rules (paste the array in https://mlocati.github.io/php-cs-fixer-configurator/#configurator to view/edit them)
        // We could move it to a class constant when we get rid of PHP 5.5 compatibility
        static $invariantRules = [
            // Each line of multi-line DocComments must have an asterisk [PSR-5] and must be aligned with the first one.
            'align_multiline_comment' => [
                'comment_type' => 'phpdocs_like',
            ],

            // Each element of an array must be indented exactly once.
            'array_indentation' => true,

            // Binary operators should be surrounded by space as configured.
            'binary_operator_spaces' => [
                'align_double_arrow' => false,
                'align_equals' => false,
            ],

            // There MUST be one blank line after the namespace declaration.
            'blank_line_after_namespace' => true,

            // An empty line feed should precede a return statement.
            // @deprecated: same as 'blank_line_before_statement' => ['statements' => ['return']],
            'blank_line_before_return' => true,

            // A single space or none should be between cast and variable.
            'cast_spaces' => true,

            // Whitespace around the keywords of a class, trait or interfaces definition should be one space.
            'class_definition' => [
                'single_line' => true,
            ],

            // Remove extra spaces in a nullable typehint.
            'compact_nullable_typehint' => true,

            // Concatenation should be spaced according configuration.
            'concat_space' => [
                'spacing' => 'one',
            ],

            // Equal sign in declare statement should be surrounded by spaces or not following configuration.
            'declare_equal_normalize' => [
                'space' => 'single',
            ],

            // The keyword `elseif` should be used instead of `else if` so that all control keywords look like single words.
            'elseif' => true,

            // PHP code MUST use only UTF-8 without BOM (remove BOM).
            'encoding' => true,

            // Replace deprecated `ereg` regular expression functions with `preg`.
            'ereg_to_preg' => true,

            // PHP code must use the long PHP open tags or short echo tags and not other tag variations.
            'full_opening_tag' => true,

            // Spaces should be properly placed in a function declaration.
            'function_declaration' => true,

            // Replace core functions calls returning constants with the constants.
            'function_to_constant' => true,

            // Add missing space between function's argument and its typehint.
            'function_typehint_space' => true,

            // Single line comments should use double slashes `//` and not hash `#`.
            // @deprecated: same as 'single_line_comment_style' => ['comment_types' => ['hash']],
            'hash_to_slash_comment' => true,

            // Function `implode` must be called with 2 arguments in the documented order.
            'implode_call' => true,

            // Include/Require and file path should be divided with a single space.
            // File path should not be placed under brackets.
            'include' => true,

            // Replaces `is_null($var)` expression with `null === $var`.
            'is_null' => [
                'use_yoda_style' => false,
            ],

            // All PHP files must use same line ending.
            'line_ending' => true,

            // Cast should be written in lower case.
            'lowercase_cast' => true,

            // The PHP constants `true`, `false`, and `null` MUST be in lower case.
            'lowercase_constants' => true,

            // PHP keywords MUST be in lower case.
            'lowercase_keywords' => true,

            // Class static references `self`, `static` and `parent` MUST be in lower case.
            'lowercase_static_reference' => true,

            // Magic constants should be referred to using the correct casing.
            'magic_constant_casing' => true,

            // Magic method definitions and calls must be using the correct casing.
            'magic_method_casing' => true,

            // In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma.
            // Argument lists MAY be split across multiple lines, where each subsequent line is indented once.
            // When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.
            'method_argument_space' => true,

            // Methods must be separated with one blank line.
            // @deprecated: same as 'class_attributes_separation' => ['method']
            'method_separation' => true,

            // Replaces `intval`, `floatval`, `doubleval`, `strval` and `boolval` function calls with according type casting operator.
            'modernize_types_casting' => true,

            // Function defined by PHP should be called using the correct casing.
            'native_function_casing' => true,

            // All instances created with new keyword must be followed by braces.
            'new_with_braces' => true,

            // Master functions shall be used instead of aliases.
            'no_alias_functions' => true,

            // There should be no empty lines after class opening brace.
            'no_blank_lines_after_class_opening' => true,

            // There must be a comment when fall-through is intentional in a non-empty case body.
            'no_break_comment' => true,

            // The closing `? >` tag MUST be omitted from files containing only PHP.
            'no_closing_tag' => true,

            // There should not be any empty comments.
            'no_empty_comment' => true,

            // There should not be empty PHPDoc blocks.
            'no_empty_phpdoc' => true,

            // Remove useless semicolon statements.
            'no_empty_statement' => true,

            // Removes extra blank lines and/or blank lines following configuration.
            'no_extra_blank_lines' => [
                'tokens' => ['curly_brace_block', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'throw', 'use'],
            ],

            // Replace accidental usage of homoglyphs (non ascii characters) in names.
            'no_homoglyph_names' => true,

            // Remove leading slashes in `use` clauses.
            'no_leading_import_slash' => true,

            // The namespace declaration line shouldn't contain leading whitespace.
            'no_leading_namespace_whitespace' => true,

            // Either language construct `print` or `echo` should be used.
            'no_mixed_echo_print' => [
                'use' => 'echo',
            ],

            // Operator `=>` should not be surrounded by multi-line whitespaces.
            'no_multiline_whitespace_around_double_arrow' => true,

            // Convert PHP4-style constructors to `__construct`.
            'no_php4_constructor' => true,

            // Short cast `bool` using double exclamation mark should not be used.
            'no_short_bool_cast' => true,

            // Single-line whitespace before closing semicolon are prohibited.
            'no_singleline_whitespace_before_semicolons' => true,

            // When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.
            'no_spaces_after_function_name' => true,

            // There MUST NOT be spaces around offset braces.
            'no_spaces_around_offset' => true,

            // There MUST NOT be a space before the closing parenthesis.
            'no_spaces_inside_parenthesis' => true,

            // Replaces superfluous `elseif` with `if`.
            'no_superfluous_elseif' => true,

            // Remove trailing commas in list function calls.
            'no_trailing_comma_in_list_call' => true,

            // PHP single-line arrays should not have trailing comma.
            'no_trailing_comma_in_singleline_array' => true,

            // Remove trailing whitespace at the end of non-blank lines.
            'no_trailing_whitespace' => true,

            // There MUST be no trailing spaces inside comment or PHPDoc.
            'no_trailing_whitespace_in_comment' => true,

            // Removes unneeded parentheses around control statements.
            'no_unneeded_control_parentheses' => true,

            // Removes unneeded curly braces that are superfluous and aren't part of a control structure's body.
            'no_unneeded_curly_braces' => true,

            // A final class must not have final methods.
            'no_unneeded_final_method' => true,

            // Unused `use` statements must be removed.
            'no_unused_imports' => true,

            // There should not be useless `else` cases.
            'no_useless_else' => true,

            // There should not be an empty `return` statement at the end of a function.
            'no_useless_return' => true,

            // In array declaration, there MUST NOT be a whitespace before each comma.
            'no_whitespace_before_comma_in_array' => true,

            // Remove trailing whitespace at the end of blank lines.
            'no_whitespace_in_blank_line' => true,

            // Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols.
            'non_printable_character' => true,

            // Array index should always be written by using square braces.
            'normalize_index_brace' => true,

            // There should not be space before or after object `T_OBJECT_OPERATOR` `->`.
            'object_operator_without_whitespace' => true,

            // Orders the elements of classes/interfaces/traits.
            'ordered_class_elements' => true,

            // Ordering `use` statements.
            'ordered_imports' => true,

            // PHPDoc should contain `@param` for all params.
            'phpdoc_add_missing_param_annotation' => [
                'only_untyped' => false,
            ],

            // PHPDoc annotation descriptions should not be a sentence.
            'phpdoc_annotation_without_dot' => true,

            // Docblocks should have the same indentation as the documented subject.
            'phpdoc_indent' => true,

            // Fix PHPDoc inline tags, make `@inheritdoc` always inline.
            'phpdoc_inline_tag' => true,

            // `@access` annotations should be omitted from PHPDoc.
            'phpdoc_no_access' => true,

            // No alias PHPDoc tags should be used.
            'phpdoc_no_alias_tag' => true,

            // `@return void` and `@return null` annotations should be omitted from PHPDoc.
            'phpdoc_no_empty_return' => true,

            // Classy that does not inherit must not have `@inheritdoc` tags.
            'phpdoc_no_useless_inheritdoc' => true,

            // Annotations in PHPDoc should be ordered so that `@param` annotations come first, then `@throws` annotations, then `@return` annotations.
            'phpdoc_order' => true,

            // The type of `@return` annotations of methods returning a reference to itself must the configured one.
            'phpdoc_return_self_reference' => true,

            // Scalar types should always be written in the same form.
            // `int` not `integer`, `bool` not `boolean`, `float` not `real` or `double`.
            'phpdoc_scalar' => true,

            // Annotations in PHPDoc should be grouped together so that annotations of the same type immediately follow each other, and annotations of a different type are separated by a single blank line.
            'phpdoc_separation' => true,

            // Single line `@var` PHPDoc should have proper spacing.
            'phpdoc_single_line_var_spacing' => true,

            // PHPDoc summary should end in either a full stop, exclamation mark, or question mark.
            'phpdoc_summary' => true,

            // PHPDoc should start and end with content, excluding the very first and last line of the docblocks.
            'phpdoc_trim' => true,

            // Removes extra blank lines after summary and after description in PHPDoc.
            'phpdoc_trim_consecutive_blank_line_separation' => true,

            // The correct case must be used for standard PHP types in PHPDoc.
            'phpdoc_types' => true,

            // Replaces `rand`, `srand`, `getrandmax` functions calls with their `mt_*` analogs.
            'random_api_migration' => true,

            // There should be one or no space before colon, and one space after it in return type declarations, according to configuration.
            'return_type_declaration' => true,

            // Inside class or interface element `self` should be preferred to the class name itself.
            'self_accessor' => true,

            // Cast shall be used, not `settype`.
            'set_type_to_cast' => true,

            // Cast `(boolean)` and `(integer)` should be written as `(bool)` and `(int)`, `(double)` and `(real)` as `(float)`, `(binary)` as `(string)`.
            'short_scalar_cast' => true,

            // A PHP file without end tag must always end with a single empty line feed.
            'single_blank_line_at_eof' => true,

            // There should be exactly one blank line before a namespace declaration.
            'single_blank_line_before_namespace' => true,

            // There MUST NOT be more than one property or constant declared per statement.
            'single_class_element_per_statement' => true,

            // There MUST be one use keyword per declaration.
            'single_import_per_statement' => true,

            // Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block.
            'single_line_after_imports' => true,

            // Convert double quotes to single quotes for simple strings.
            'single_quote' => true,

            // Fix whitespace after a semicolon.
            'space_after_semicolon' => true,

            // Replace all `<>` with `!=`.
            'standardize_not_equals' => true,

            // A case should be followed by a colon and not a semicolon.
            'switch_case_semicolon_to_colon' => true,

            // Removes extra spaces between colon and case value.
            'switch_case_space' => true,

            // Standardize spaces around ternary operator.
            'ternary_operator_spaces' => true,

            // PHP multi-line arrays should have a trailing comma.
            'trailing_comma_in_multiline_array' => true,

            // Arrays should be formatted like function/method arguments, without leading or trailing single line space.
            'trim_array_spaces' => true,

            // Unary operators should be placed adjacent to their operands.
            'unary_operator_spaces' => true,

            // Visibility MUST be declared on all properties and methods; `abstract` and `final` MUST be declared before the visibility; `static` MUST be declared after the visibility.
            'visibility_required' => true,

            // In array declaration, there MUST be a whitespace after each comma.
            'whitespace_after_comma_in_array' => true,
        ];

        $rules = $invariantRules + [
            // PHP arrays should be declared using the configured syntax.
            'array_syntax' => [
                'syntax' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '5.4') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? 'long' : 'short',
            ],

            // Ensure there is no code on the same line as the PHP open tag and it is followed by a blank line.
            'blank_line_after_opening_tag' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? true : false,

            // The body of each structure MUST be enclosed by braces.
            // Braces should be properly placed.
            // Body of braces should be properly indented.
            'braces' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? ['allow_single_line_closure' => true] : false,

            // Replaces `dirname(__FILE__)` expression with equivalent `__DIR__` constant.
            'dir_constant' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '5.3') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? false : true,

            // Code MUST use configured indentation type.
            'indentation_type' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? true : false,

            // Ensure there is no code on the same line as the PHP open tag.
            'linebreak_after_opening_tag' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? true : false,

            // Replace short-echo `<?=` with long format `<?php echo` syntax.
            'no_short_echo_tag' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '5.4') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? true : false,

            // Class names should match the file name.
            'psr4' => $hasFlag(PhpFixer::FLAG_PSR4CLASS) ? true : false,

            // There should not be blank lines between docblock and the documented element.
            'no_blank_lines_after_phpdoc' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? true : false,

            // `@var` and `@type` annotations should not contain the variable name.
            'phpdoc_var_without_name' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? true : false,
        ];

        if ($onlyAvailableOnes) {
            $fixerFactory = $this->getFixerFactory();
            foreach (array_keys($rules) as $ruleName) {
                if ($ruleName[0] !== '@' && !$fixerFactory->hasRule($ruleName)) {
                    unset($rules[$ruleName]);
                }
            }
        }

        return $rules;
    }

    /**
     * Resolve the fixers associated to the rules.
     *
     * @param array $rules the list of the rules (as returned by the getRules method)
     *
     * @return \PhpCsFixer\Fixer\FixerInterface[]
     */
    public function getFixers(array $rules)
    {
        return $this->getFixerFactory()
            ->useRuleSet(RuleSet::create($rules))
            ->setWhitespacesConfig(new WhitespacesFixerConfig())
            ->getFixers()
        ;
    }

    /**
     * @return \PhpCsFixer\FixerFactory
     */
    protected function getFixerFactory()
    {
        if (empty($this->fixerFactory->getFixers())) {
            $this->fixerFactory->registerBuiltInFixers();
        }

        return $this->fixerFactory;
    }
}
