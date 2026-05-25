"""
generate_context.py
-------------------
Collects all relevant source files from the longtail-WM Laravel project
and writes them into a single context.txt file — ready to paste into
Claude Projects, ChatGPT, or any AI tool.

Usage:
    python generate_context.py
    python generate_context.py --output my_context.txt
    python generate_context.py --max-file-kb 100
"""

import os
import argparse
from pathlib import Path
from datetime import datetime

# ─── Configuration ────────────────────────────────────────────────────────────

# Directories to completely skip
SKIP_DIRS = {
    "vendor",
    "node_modules",
    ".git",
    "storage",
    "bootstrap/cache",
    ".idea",
    ".vscode",
    "__pycache__",
}

# File extensions to include
INCLUDE_EXTENSIONS = {
    # PHP
    ".php",
    # Blade templates
    ".blade.php",
    # Frontend
    ".js", ".ts", ".vue", ".css", ".scss",
    # Config & meta
    ".json", ".yaml", ".yml", ".env.example",
    # Routes, markdown, etc.
    ".md", ".xml",
}

# Specific filenames to always include (regardless of extension)
ALWAYS_INCLUDE_FILES = {
    "composer.json",
    "package.json",
    "vite.config.js",
    ".env.example",
    "phpunit.xml",
    "CLAUDE.md",
    "README.md",
}

# Specific filenames to always skip
ALWAYS_SKIP_FILES = {
    "composer.lock",
    "package-lock.json",
    ".env",          # Never include real env — secrets!
    "yarn.lock",
}

# Directories that are high-priority (listed first in output)
PRIORITY_DIRS = [
    "app",
    "routes",
    "database/migrations",
    "database/seeders",
    "resources/views",
    "config",
]

# ─── Helpers ──────────────────────────────────────────────────────────────────

def should_skip_dir(rel_path: str) -> bool:
    parts = Path(rel_path).parts
    for skip in SKIP_DIRS:
        skip_parts = Path(skip).parts
        # Check if any segment of rel_path starts with the skip dir
        for i in range(len(parts)):
            if parts[i:i+len(skip_parts)] == skip_parts:
                return True
    return False


def should_include_file(file_path: Path, max_bytes: int) -> bool:
    name = file_path.name
    suffix = file_path.suffix.lower()

    if name in ALWAYS_SKIP_FILES:
        return False

    if name in ALWAYS_INCLUDE_FILES:
        return file_path.stat().st_size <= max_bytes

    # Blade files have compound extension (.blade.php)
    if file_path.name.endswith(".blade.php"):
        return file_path.stat().st_size <= max_bytes

    if suffix in INCLUDE_EXTENSIONS:
        return file_path.stat().st_size <= max_bytes

    return False


def collect_files(root: Path, max_bytes: int) -> list[Path]:
    """Walk the project and return all relevant files, priority dirs first."""
    priority_files: list[Path] = []
    other_files: list[Path] = []

    for dirpath, dirnames, filenames in os.walk(root):
        rel_dir = os.path.relpath(dirpath, root)

        # Prune skip dirs in-place so os.walk doesn't recurse into them
        dirnames[:] = [
            d for d in dirnames
            if not should_skip_dir(os.path.join(rel_dir, d))
        ]

        is_priority = any(
            rel_dir.startswith(p.replace("/", os.sep)) or rel_dir == p.replace("/", os.sep)
            for p in PRIORITY_DIRS
        )

        for filename in sorted(filenames):
            file_path = Path(dirpath) / filename
            if should_include_file(file_path, max_bytes):
                if is_priority:
                    priority_files.append(file_path)
                else:
                    other_files.append(file_path)

    return priority_files + other_files


def format_file_block(file_path: Path, root: Path) -> str:
    rel = os.path.relpath(file_path, root)
    sep = "=" * 80
    try:
        content = file_path.read_text(encoding="utf-8", errors="replace")
    except Exception as e:
        content = f"[ERROR reading file: {e}]"

    ext = file_path.suffix.lstrip(".")
    if file_path.name.endswith(".blade.php"):
        ext = "blade"
    elif ext == "php":
        ext = "php"

    return (
        f"\n{sep}\n"
        f"FILE: {rel}\n"
        f"{sep}\n"
        f"```{ext}\n"
        f"{content}\n"
        f"```\n"
    )


def generate_tree(root: Path, files: list[Path]) -> str:
    """Generate a simple directory tree of included files."""
    lines = [f"{root.name}/"]
    rel_paths = sorted(os.path.relpath(f, root) for f in files)
    dirs_seen = set()

    for rel in rel_paths:
        parts = Path(rel).parts
        for depth, part in enumerate(parts[:-1]):
            dir_key = parts[:depth+1]
            if dir_key not in dirs_seen:
                dirs_seen.add(dir_key)
                indent = "  " * (depth + 1)
                lines.append(f"{indent}[+] {part}/")
        indent = "  " * len(parts)
        lines.append(f"{indent}    {parts[-1]}")

    return "\n".join(lines)


# ─── Main ─────────────────────────────────────────────────────────────────────

def main():
    parser = argparse.ArgumentParser(description="Generate context.txt for longtail-WM")
    parser.add_argument("--output", default="context.txt", help="Output file name")
    parser.add_argument("--max-file-kb", type=int, default=150,
                        help="Max file size in KB to include (default: 150)")
    args = parser.parse_args()

    root = Path(__file__).parent.resolve()
    max_bytes = args.max_file_kb * 1024
    output_path = root / args.output

    print(f"[SCAN] Scanning: {root}")
    print(f"[MAX]  Max file size: {args.max_file_kb} KB")

    files = collect_files(root, max_bytes)

    print(f"[OK]   Found {len(files)} files to include")

    header = f"""# longtail-WM — Full Project Context
# Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
# Root: {root}
# Files included: {len(files)}
# Stack: Laravel 13 · PHP 8.3 · Livewire 4 · Spatie Permissions · Sanctum · Vite
#
# HOW TO USE:
#   Paste this file into Claude Projects > Project Knowledge
#   or upload it directly in any AI chat for full codebase context.
{"=" * 80}

## PROJECT FILE TREE (included files)

"""
    tree = generate_tree(root, files)

    with open(output_path, "w", encoding="utf-8") as out:
        out.write(header)
        out.write(tree)
        out.write("\n\n")
        out.write("=" * 80)
        out.write("\n## SOURCE FILES\n")

        for file_path in files:
            rel = os.path.relpath(file_path, root)
            print(f"  ->  {rel}")
            out.write(format_file_block(file_path, root))

    size_kb = output_path.stat().st_size / 1024
    print(f"\n[DONE] context.txt written -> {output_path}")
    print(f"       Size: {size_kb:.1f} KB")
    print(f"\n[TIP]  Upload '{args.output}' to Claude Project > Project Knowledge")


if __name__ == "__main__":
    main()
