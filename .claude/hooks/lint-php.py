#!/usr/bin/env python3
"""PostToolUse-hook: controleer de syntax van het bestand dat net is aangeraakt.

Leest de hook-invoer (JSON) van stdin. Voor .php draait `php -l`, voor .js
`node --check`. Bij een fout: exitcode 2 met de melding op stderr, zodat Claude
hem ziet. Andere bestanden laat de hook bewust door. Vindt hij geen werkende
interpreter, dan zegt hij dat hardop (stderr, exit 0) in plaats van stil te
falen.
"""
import json
import os
import subprocess
import sys


def werkende(kandidaten, probe):
    for kandidaat in kandidaten:
        try:
            r = subprocess.run([kandidaat] + probe, capture_output=True, timeout=10)
            if r.returncode == 0:
                return kandidaat
        except (OSError, subprocess.SubprocessError):
            continue
    return None


def main():
    try:
        data = json.load(sys.stdin)
    except ValueError:
        print("lint-php hook: kon de invoer niet lezen", file=sys.stderr)
        return 0

    pad = (data.get("tool_input") or {}).get("file_path", "")
    if not pad or not os.path.isfile(pad):
        return 0

    if pad.endswith(".php"):
        php = os.environ.get("PHP_BIN") or werkende(
            ["php", "/opt/homebrew/bin/php", "/usr/bin/php"], ["-r", "exit(0);"]
        )
        if not php:
            print("lint-php hook: geen werkende PHP gevonden, syntax NIET gecontroleerd", file=sys.stderr)
            return 0
        cmd = [php, "-l", pad]
    elif pad.endswith(".js"):
        node = werkende(["node"], ["--version"])
        if not node:
            print("lint-php hook: geen werkende node gevonden, syntax NIET gecontroleerd", file=sys.stderr)
            return 0
        cmd = [node, "--check", pad]
    else:
        return 0

    r = subprocess.run(cmd, capture_output=True, text=True)
    if r.returncode != 0:
        print("Syntaxfout in %s:\n%s%s" % (pad, r.stdout, r.stderr), file=sys.stderr)
        return 2
    return 0


if __name__ == "__main__":
    sys.exit(main())
