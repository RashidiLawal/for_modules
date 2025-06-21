#!/bin/sh

# Directory where this script is located (.github folder)
SCRIPT_DIR="$( cd "$( dirname "$0" )" && pwd )"

# Root directory of your project (assuming 'hooks' folder is here)
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Create .git/hooks directory if not exists
mkdir -p "${PROJECT_ROOT}/.git/hooks"

# Copy pre-commit hook to .git/hooks
cp -f "${PROJECT_ROOT}/.github/hooks/pre-commit" "${PROJECT_ROOT}/.git/hooks/pre-commit"

# Make pre-commit hook executable
chmod +x "${PROJECT_ROOT}/.git/hooks/pre-commit"

echo "Pre-commit hook installed successfully!"

