# Use an official PHP image with the Apache web server
FROM php:8.2-apache

# ----------------------------------------------------
# 1. Install Python3 and create a symbolic link for 'python'
# ----------------------------------------------------
RUN apt-get update && \
    apt-get install -y \
        python3 \
        python3-pip \
        # Install dependencies for numpy/torch/matplotlib
        build-essential \
        libblas-dev \
        liblapack-dev \
        python3-dev \
        libjpeg-dev \
        zlib1g-dev \
    --no-install-recommends && \
    # Create a symbolic link for 'python' command compatibility with index.php
    ln -s /usr/bin/python3 /usr/bin/python && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# ----------------------------------------------------
# 2. Install Node.js (and npm) using a consolidated RUN command
#    This method is more robust for adding external repositories.
# ----------------------------------------------------
RUN apt-get update && \
    apt-get install -y curl gnupg ca-certificates && \
    # 1. Add NodeSource GPG key and repository
    mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list > /dev/null && \
    # 2. Update and install Node.js (which includes npm)
    apt-get update && \
    apt-get install -y nodejs \
    --no-install-recommends && \
    # 3. Cleanup
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# ----------------------------------------------------
# 3. Install Python Dependencies
# ----------------------------------------------------
# Set the working directory for copying the requirements file
WORKDIR /var/www/html/

# Copy requirements.txt and install Python dependencies
COPY requirements.txt .
RUN pip3 install --no-cache-dir -r requirements.txt

# ----------------------------------------------------
# 4. Final setup for the application
# ----------------------------------------------------

# Copy your application code into the web root directory of the container
COPY . /var/www/html/

# Expose the default Apache port (Render automatically maps this)
EXPOSE 80

# CRITICAL CHANGE: Override the base image's CMD to run the Flask app via Gunicorn.
# The format is 'app_file:flask_instance_name', which is 'app:myApp' based on app.py.
# Bind to 0.0.0.0 on port 80 (standard HTTP port in the container).
CMD ["gunicorn", "--bind", "0.0.0.0:80", "app:myApp"]
