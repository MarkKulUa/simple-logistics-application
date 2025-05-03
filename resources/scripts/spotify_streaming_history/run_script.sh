#!/bin/bash
# chmod +x run_script.sh
# ./run_script.sh

# Install Node.js
NODE_VERSION=18.20.2

# Checking nvm
if [ -z "$(command -v nvm)" ]; then
  echo "nvm not installed. Installing..."
  curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
  source ~/.nvm/nvm.sh
fi

nvm install $NODE_VERSION
nvm use $NODE_VERSION

# Installing relations
npm install axios xlsx csv-stringify bottleneck cli-progress

# Script running
node index.js
