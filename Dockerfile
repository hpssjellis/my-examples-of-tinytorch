# Use a Python base image
FROM python:3.11-slim

# Set environment variables
ENV PYTHONUNBUFFERED 1
ENV APP_HOME /usr/src/app

# Create app directory and set as working directory
RUN mkdir $APP_HOME
WORKDIR $APP_HOME

# Install system dependencies needed for tinytorch and building packages
# We need git, build-essential, and libpq-dev (often needed for database connections)
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        git \
        build-essential \
        libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install TinyTorch, Flask, and common ML utility dependencies from requirements.txt
# This now includes: tinytorch, numpy, flask, gunicorn, matplotlib, scikit-learn, pillow, tqdm.
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Clone the TinyTorch repository and run the setup script for the environment
# This is crucial for tito to find the course files and config.
RUN git clone https://github.com/mlsysbook/TinyTorch.git /usr/src/TinyTorch
WORKDIR /usr/src/TinyTorch
# Run the setup script to configure tito/tinytorch environment
RUN ./setup-environment.sh

# Go back to our main application directory
WORKDIR $APP_HOME

# Copy the Flask application and student files
COPY . $APP_HOME

# Expose the port Gunicorn/Flask will run on
EXPOSE 8080

# Command to run the application using Gunicorn (a production WSGI server)
CMD ["gunicorn", "--bind", "0.0.0.0:8080", "app:myApp"]
