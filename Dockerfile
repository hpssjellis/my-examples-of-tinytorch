# Start with a standard Python base image
FROM python:3.9-slim

# Set environment variables
ENV PYTHONUNBUFFERED=1 \
    PYTHONDONTWRITEBYTECODE=1 \
    TITO_ROOT=/app/tinytorch

# Install core build dependencies
# We need git to clone and procps for some Jupyter/Tito background tasks
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    procps \
    && rm -rf /var/lib/apt/lists/*

# Set the working directory
WORKDIR /app

# 1. Clone the repository
# TinyTorch is part of the cs249r_book repo
RUN git clone --depth 1 https://github.com/harvard-edge/cs249r_book.git .

# 2. Navigate into the TinyTorch component
WORKDIR $TITO_ROOT

# 3. Upgrade pip and install all core dependencies in one layer
# Including nbdev and jupytext which are required for the tito workflow
RUN python -m pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir \
    numpy \
    jupyterlab \
    rich \
    torch \
    pyyaml \
    pytest \
    jupytext \
    nbdev \
    nbgrader

# 4. Install TinyTorch in Development (Editable) Mode
# This enables the 'tito' command and links the source code
RUN pip install -e .

# 5. Optional: Initialize nbgrader if you are using instructor features
# RUN tito nbgrader init

# Ensure the tito command is in the path and accessible
ENV PATH="${PATH}:/root/.local/bin"

# The book recommends working from the inner tinytorch directory
WORKDIR $TITO_ROOT

# Verify installation on startup
CMD ["bash", "-c", "tito system health && exec bash"]
