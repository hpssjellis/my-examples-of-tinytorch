FROM python:3.9-slim

# Install PHP, Node, and Git
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl git procps php-cli php-mbstring php-xml \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

ENV PYTHONUNBUFFERED=1 PORT=8080 TITO_ROOT=/app/tinytorch
WORKDIR /app

# Setup TinyTorch
RUN git clone --depth 1 https://github.com/harvard-edge/cs249r_book.git ./repo_temp && \
    mv ./repo_temp/tinytorch $TITO_ROOT && \
    rm -rf ./repo_temp

WORKDIR $TITO_ROOT
RUN python -m pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir numpy jupyterlab rich torch pyyaml pytest jupytext nbdev nbgrader && \
    pip install -e .

# Prepare the PHP application
WORKDIR /app
COPY . .
RUN npm install

# Give PHP permission to run the tito CLI and write to the app folder
RUN chown -R www-data:www-data /app && chmod -R 755 /app

EXPOSE 8080 8888
CMD ["npm", "start"]
