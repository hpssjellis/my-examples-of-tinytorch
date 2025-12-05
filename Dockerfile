# Use an official PHP image with the Apache web server
FROM php:8.2-apache

# Set environment variables
ENV PYTHONUNBUFFERED=1
ENV TITO_ROOT=/TinyTorch
ENV PATH="${PATH}:/root/.local/bin"
ENV DEBIAN_FRONTEND=noninteractive

# ----------------------------------------------------
# 1. Install System Dependencies and Upgrade pip in one step
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
        wget \
    --no-install-recommends && \
    # Create symbolic link for 'python' command compatibility
    ln -s /usr/bin/python3 /usr/bin/python && \
    # Upgrade pip using get-pip.py (more reliable than apt's pip)
    wget https://bootstrap.pypa.io/get-pip.py && \
    python3 get-pip.py && \
    rm get-pip.py && \
    pip --version && \
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
# 3. Clone and Install TinyTorch
# ----------------------------------------------------
RUN git clone https://github.com/mlsysbook/TinyTorch.git $TITO_ROOT

WORKDIR $TITO_ROOT

# Install TinyTorch dependencies one by one for better error tracking
RUN pip install --no-cache-dir numpy && \
    pip install --no-cache-dir jupyter && \
    pip install --no-cache-dir jupyterlab && \
    pip install --no-cache-dir rich && \
    pip install --no-cache-dir torch && \
    pip install --no-cache-dir pyyaml && \
    pip install --no-cache-dir pytest && \
    pip install --no-cache-dir jupytext && \
    pip install --no-cache-dir nbgrader

# Install TinyTorch in editable mode (enables 'tito' CLI)
RUN pip install -e .

# ----------------------------------------------------
# 4. Setup PHP Application
# ----------------------------------------------------
# Return to root for PHP application setup
WORKDIR /var/www/html

# Copy your PHP application code
COPY . /var/www/html/

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# ----------------------------------------------------
# 5. Expose Ports
# ----------------------------------------------------
# Port 80 for Apache/PHP
EXPOSE 80
# Port 8888 for Jupyter (if you want to run notebooks)
EXPOSE 8888

# ----------------------------------------------------
# 6. Startup Configuration
# ----------------------------------------------------
# Create a startup script to run both Apache and optionally Jupyter
RUN echo '#!/bin/bash\n\
# Start Apache in the background\n\
apache2-foreground &\n\
# Optionally start Jupyter Lab (uncomment if needed)\n\
# jupyter lab --ip=0.0.0.0 --port=8888 --no-browser --allow-root --notebook-dir=/var/www/html &\n\
# Keep container running\n\
wait' > /start.sh && chmod +x /start.sh

# Default command runs Apache (and optionally Jupyter)
CMD ["/start.sh"]
