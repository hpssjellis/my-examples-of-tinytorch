# Use an official PHP image with the Apache web server
FROM php:8.2-apache

# Set environment variables
ENV PYTHONUNBUFFERED=1
ENV TITO_ROOT=/TinyTorch
ENV PATH="${PATH}:/root/.local/bin"

# ----------------------------------------------------
# 1. Install System Dependencies (Python, Node.js, Git, Build Tools)
# ----------------------------------------------------
RUN apt-get update && \
    apt-get install -y \
        python3 \
        python3-pip \
        python3-venv \
        curl \
        gnupg \
        ca-certificates \
        git \
        build-essential \
    --no-install-recommends && \
    # Create symbolic link for 'python' command compatibility
    ln -s /usr/bin/python3 /usr/bin/python && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# ----------------------------------------------------
# 2. Install Node.js (npm included)
# ----------------------------------------------------
RUN mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list > /dev/null && \
    apt-get update && \
    apt-get install -y nodejs --no-install-recommends && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# ----------------------------------------------------
# 3. Upgrade pip
# ----------------------------------------------------
RUN python3 -m pip install --no-cache-dir --upgrade pip

# ----------------------------------------------------
# 4. Clone and Install TinyTorch
# ----------------------------------------------------
RUN git clone https://github.com/mlsysbook/TinyTorch.git $TITO_ROOT

WORKDIR $TITO_ROOT

# Install TinyTorch dependencies
RUN pip install --no-cache-dir \
    numpy \
    jupyter \
    jupyterlab \
    rich \
    torch \
    pyyaml \
    pytest \
    jupytext \
    nbgrader

# Install TinyTorch in editable mode (enables 'tito' CLI)
RUN pip install -e .

# ----------------------------------------------------
# 5. Setup PHP Application
# ----------------------------------------------------
# Return to root for PHP application setup
WORKDIR /var/www/html

# Copy your PHP application code
COPY . /var/www/html/
