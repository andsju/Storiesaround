#!/usr/bin/env python3
"""
fix_php_bugs.py

Reusable script to bulk-fix known PHP 7.4 -> 8.3 migration bug patterns
in this codebase (see prompt.md for the full documented bug catalogue).

Currently automates the two patterns that are safe to fix mechanically,
everywhere, without per-occurrence context:

  - "VALIDATE_INT":
        filter_input(INPUT_POST, 'x', FILTER_VALIDATE_INT) ? $_POST['x'] : null|0;
        -> (int) filter_input(INPUT_POST, 'x', FILTER_VALIDATE_INT);

  - "trim null":
        trim($_POST['x'])
        -> trim($_POST['x'] ?? '')

NOTE: The "strlen" and "valid datetime" patterns from prompt.md are
intentionally NOT automated here. Fixing those correctly requires
verifying, per occurrence, whether the underlying database column
actually allows NULL (see the manual analysis notes for
pages_edit_ajax.php, where several look-alike matches were confirmed
to be false positives). Apply those two patterns manually, case by case.

Usage:
    python tools/fix_php_bugs.py <file1.php> [file2.php ...]

Safety notes:
  - Lines that are commented out (start with // after trimming
    whitespace) are skipped.
  - The script is idempotent: running it again on an already-fixed
    file reports 0 additional hits.
  - For every VALIDATE_INT fix, the script checks whether the
    assigned variable is later used with isset($var) anywhere in the
    file, and prints a WARNING if so. Casting to (int) makes the
    variable always "set" (0 instead of null), which can silently
    change isset()-based control flow. Review those cases manually,
    e.g. (see pages_edit_ajax.php lines 1835 and 2224 for real
    examples already handled this way):

        $var = filter_input(INPUT_POST, 'x', FILTER_VALIDATE_INT);
        $var = is_int($var) ? $var : null;
"""
import re
import sys


VALIDATE_INT_PATTERN = re.compile(
    r"filter_input\(INPUT_POST, '(\w+)', FILTER_VALIDATE_INT\)\s*\?\s*\$_POST\['\1'\]\s*:\s*(?:null|0)"
)

TRIM_NULL_PATTERN = re.compile(
    r"trim\(\$_POST\[(['\"])(\w+)\1\]\)"
)


def fix_validate_int(line):
    def repl(m):
        return "(int) filter_input(INPUT_POST, '{}', FILTER_VALIDATE_INT)".format(m.group(1))
    return VALIDATE_INT_PATTERN.subn(repl, line)


def fix_trim_null(line):
    def repl(m):
        quote, key = m.group(1), m.group(2)
        return "trim($_POST[{q}{k}{q}] ?? '')".format(q=quote, k=key)
    return TRIM_NULL_PATTERN.subn(repl, line)


def extract_assigned_var(original_line):
    """Best-effort: pull the left-hand-side variable name from a fixed
    line (e.g. '$pages_id = filter_input(...)...') so we can later check
    whether it's used with isset() elsewhere in the file."""
    m = re.match(r"\s*(\$\w+)\s*=", original_line)
    return m.group(1) if m else None


def process_file(path):
    with open(path, 'r', encoding='utf-8') as f:
        lines = f.readlines()

    validate_int_hits = []
    trim_null_hits = []
    isset_vars_to_check = []

    for i, line in enumerate(lines):
        stripped = line.strip()
        if stripped.startswith('//'):
            continue

        original_line = line

        new_line, n1 = fix_validate_int(line)
        if n1:
            var_name = extract_assigned_var(original_line)
            if var_name:
                isset_vars_to_check.append((i + 1, var_name))
            line = new_line
            validate_int_hits.append(i + 1)

        new_line, n2 = fix_trim_null(line)
        if n2:
            line = new_line
            trim_null_hits.append(i + 1)

        lines[i] = line

    with open(path, 'w', encoding='utf-8', newline='') as f:
        f.writelines(lines)

    full_text = ''.join(lines)
    warnings = []
    for line_no, var_name in isset_vars_to_check:
        if 'isset({})'.format(var_name) in full_text:
            warnings.append((line_no, var_name))

    return validate_int_hits, trim_null_hits, warnings


def main():
    if len(sys.argv) < 2:
        print("Usage: python tools/fix_php_bugs.py <file1.php> [file2.php ...]")
        sys.exit(1)

    for path in sys.argv[1:]:
        print("=== {} ===".format(path))
        validate_int_hits, trim_null_hits, warnings = process_file(path)
        print("  VALIDATE_INT fixes: {} (lines: {})".format(len(validate_int_hits), validate_int_hits))
        print("  trim null fixes:    {} (lines: {})".format(len(trim_null_hits), trim_null_hits))
        if warnings:
            print("  WARNING: the variable(s) below are used with isset() later in the file.")
            print("           Casting to (int) makes the variable ALWAYS set (0 instead of")
            print("           null), which can change isset()-based control flow. Review")
            print("           manually, e.g.:")
            print("             $var = filter_input(INPUT_POST, 'x', FILTER_VALIDATE_INT);")
            print("             $var = is_int($var) ? $var : null;")
            for line_no, var_name in warnings:
                print("           - line {}: {}".format(line_no, var_name))


if __name__ == '__main__':
    main()
