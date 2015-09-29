#!/usr/bin/env bash

# This script builds a release of the extension by compiling production assets,
# removing development files, and zipping up the result. It should be run from
# the root directory of the extension.

base=$PWD
release=/tmp/extension

# Make a copy of the extension files
rm -rf ${release}
mkdir ${release}
git archive --format tar --worktree-attributes HEAD | tar -xC ${release}

# Compile assets
cd ${release}
bash scripts/compile.sh

# Delete files
rm -rf scripts
rm -rf `find . -type d -name node_modules`
rm -rf `find . -type d -name bower_components`

# Set file permissions
find . -type d -exec chmod 0750 {} +
find . -type f -exec chmod 0644 {} +
chmod 0775 .

# Create the release archive
zip -r extension.zip ./
mv extension.zip ${base}/release.zip
