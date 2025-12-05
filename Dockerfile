# Start with a standard Python base image
FROM python:3.9-slim

# Set environment variables
ENV PYTHONUNBUFFERED 1
ENV TITO_ROOT /TinyTorch

# Install core build dependencies (like git for cloning)
RUN apt-get update && apt-get install -y \
    git \
    && rm -rf /var/lib/apt/lists/*

# Set the working directory
WORKDIR /app

# 1. Clone the TinyTorch repository
RUN git clone https://github.com/mlsysbook/TinyTorch.git $TITO_ROOT

# Navigate into the project directory
WORKDIR $TITO_ROOT

# 2. Upgrade pip (standard practice)
RUN python -m pip install --no-cache-dir --upgrade pip

# 3. Install Explicit Core Dependencies
# The automated setup installs NumPy, Jupyter, Rich, PyTorch for validation, PyYAML, pytest, and jupytext [1, 2].
# We install these individually based on the components mentioned:
RUN pip install --no-cache-dir \
    NumPy \
    Jupyter \
    Rich \
    PyTorch \
    PyYAML \
    pytest \
    jupytext

# 4. Install NBGrader (Required for full course/instructor functionality)
# NBGrader is installed separately in the manual setup [3].
RUN pip install --no-cache-dir nbgrader

# 5. Configure TinyTorch in Development (Editable) Mode
# This is crucial for enabling the 'tito' CLI and linking notebooks to the package [1, 2].
RUN pip install -e .

# Set the entrypoint to keep the container running
CMD ["bash"]
