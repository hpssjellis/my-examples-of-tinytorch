# Use an official PHP image with the Apache web server
FROM php:8.2-apache

# Set environment variables
ENV PYTHONUNBUFFERED=1
ENV TITO_ROOT=/TinyTorch
ENV PATH="${PATH}:/root/.local/bin"
ENV DEBIAN_FRONTEND=noninteractive

# ----------------------------------------------------
# 1. Install System Dependencies
# ----------------------------------------------------
RUN apt-get update && \
    apt-get install -y \
        python3 \
        python3-pip \
        python3-dev \
        python3-venv \
        curl \
        gnupg \
        ca-certificates \
        git \
        build-essential \
    --no-install-recommends && \
    ln -s /usr/bin/python3 /usr/bin/python && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# ----------------------------------------------------
# 2. Install Node.js
# ----------------------------------------------------
RUN mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list > /dev/null && \
    apt-get update && \
    apt-get install -y nodejs --no-install-recommends && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# ----------------------------------------------------
# 3. Clone TinyTorch
# ----------------------------------------------------
RUN git clone https://github.com/mlsysbook/TinyTorch.git $TITO_ROOT

WORKDIR $TITO_ROOT

# ----------------------------------------------------
# 4. Install Python packages
# ----------------------------------------------------
# Set pip configuration to suppress root warning in Docker
ENV PIP_ROOT_USER_ACTION=ignore

# Install packages with --break-system-packages flag for Debian 12+
RUN python3 -m pip install --break-system-packages --no-cache-dir \
    numpy \
    jupyter \
    jupyterlab \
    rich \
    torch \
    pyyaml \
    pytest \
    jupytext \
    nbgrader

# Install TinyTorch in editable mode
RUN python3 -m pip install --break-system-packages -e .

# ----------------------------------------------------
# 5. Setup PHP Application
# ----------------------------------------------------
WORKDIR /var/www/html

# Copy your PHP application code
COPY . /var/www/html/

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# ----------------------------------------------------
# 6. Expose Ports
# ----------------------------------------------------
EXPOSE 80
EXPOSE 8888

# ----------------------------------------------------
# 7. Startup Script
# ----------------------------------------------------
RUN echo '#!/bin/bash\n\
apache2-foreground &\n\
wait' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
