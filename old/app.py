from flask import Flask, request, render_template_string, redirect, url_for
import subprocess
from typing import Dict, Any, Optional, Tuple, List

# Following user preference: Descriptive my* camelCase variable names
myApp = Flask(__name__) 
myMagicWord = 'fred'

# --- 1. COURSE MODULES DEFINITION ---
# IMPORTANT: The 'myTitoCommand' now directly references the command we want to run
# We map all 20 modules to their corresponding tito command (e.g., '01', '02', '19', '20')
COURSE_MODULES: Dict[int, Dict[str, Any]] = {
    # 🏗 Foundation Tier (01-07)
    1: {
        'myTitle': 'Tensor',
        'myPythonFile': 'c01_tensor.py',
        'myTitoCommand': 'tito module complete 01', 
        'myTFJSFile': 'c01_tensor_tfjs.html', 
        'myDescription': 'N-Dimensional Arrays & Basic Operations.',
        'myColor': '#007bff'
    },
    2: {
        'myTitle': 'Activations',
        'myPythonFile': 'c02_activations.py', 
        'myTitoCommand': 'tito module complete 02', 
        'myTFJSFile': 'c02_activations_tfjs.html', 
        'myDescription': 'Implement common non-linear activation functions (e.g., ReLU, Sigmoid).',
        'myColor': '#28a745'
    },
    3: {
        'myTitle': 'Layers',
        'myPythonFile': 'c03_layers.py',
        'myTitoCommand': 'tito module complete 03', 
        'myTFJSFile': 'c03_layers_tfjs.html',
        'myDescription': 'Building blocks: Linear, Sequential, and other layer types.',
        'myColor': '#ffc107'
    },
    4: {
        'myTitle': 'Losses',
        'myPythonFile': 'c04_losses.py',
        'myTitoCommand': 'tito module complete 04', 
        'myTFJSFile': 'c04_losses_tfjs.html',
        'myDescription': 'Implement loss functions like MSE and Cross-Entropy.',
        'myColor': '#dc3545'
    },
    5: {
        'myTitle': 'Autograd',
        'myPythonFile': 'c05_autograd.py',
        'myTitoCommand': 'tito module complete 05', 
        'myTFJSFile': 'c05_autograd_tfjs.html',
        'myDescription': 'Implement the foundation of automatic differentiation (backward pass).',
        'myColor': '#17a2b8'
    },
    6: {
        'myTitle': 'Optimizers',
        'myPythonFile': 'c06_optimizers.py',
        'myTitoCommand': 'tito module complete 06', 
        'myTFJSFile': 'c06_optimizers_tfjs.html',
        'myDescription': 'Build optimizers like SGD and Adam.',
        'myColor': '#6f42c1'
    },
    7: {
        'myTitle': 'Training',
        'myPythonFile': 'c07_training.py',
        'myTitoCommand': 'tito module complete 07', 
        'myTFJSFile': 'c07_training_tfjs.html',
        'myDescription': 'Putting it all together: the training loop, data batching, and evaluation.',
        'myColor': '#e83e8c'
    },
    
    # 🏛️ Architecture Tier (08-13) - Using a slight variation in color for this tier
    8: {
        'myTitle': 'DataLoader',
        'myPythonFile': 'c08_dataloader.py',
        'myTitoCommand': 'tito module complete 08', 
        'myTFJSFile': None,
        'myDescription': 'Efficiently load and preprocess data for training.',
        'myColor': '#00bcd4'
    },
    9: {
        'myTitle': 'Convolutions',
        'myPythonFile': 'c09_convolutions.py',
        'myTitoCommand': 'tito module complete 09', 
        'myTFJSFile': 'c09_convolutions_tfjs.html',
        'myDescription': 'Implement 2D convolution and pooling operations.',
        'myColor': '#4CAF50'
    },
    10: {
        'myTitle': 'Tokenization',
        'myPythonFile': 'c10_tokenization.py',
        'myTitoCommand': 'tito module complete 10', 
        'myTFJSFile': 'c10_tokenization_tfjs.html',
        'myDescription': 'Convert raw text into numerical tokens for NLP models.',
        'myColor': '#ff9800'
    },
    11: {
        'myTitle': 'Embeddings',
        'myPythonFile': 'c11_embeddings.py',
        'myTitoCommand': 'tito module complete 11', 
        'myTFJSFile': 'c11_embeddings_tfjs.html',
        'myDescription': 'Representing tokens as dense vectors (Word Embeddings).',
        'myColor': '#9c27b0'
    },
    12: {
        'myTitle': 'Attention',
        'myPythonFile': 'c12_attention.py',
        'myTitoCommand': 'tito module complete 12', 
        'myTFJSFile': 'c12_attention_tfjs.html',
        'myDescription': 'The core mechanism for Transformer models.',
        'myColor': '#3f51b5'
    },
    13: {
        'myTitle': 'Transformers',
        'myPythonFile': 'c13_transformers.py',
        'myTitoCommand': 'tito module complete 13', 
        'myTFJSFile': 'c13_transformers_tfjs.html',
        'myDescription': 'Assembling the Encoder and Decoder blocks for sequence tasks.',
        'myColor': '#795548'
    },

    # ⏱️ Optimization Tier (14-19) - Using a further variation in color
    14: {
        'myTitle': 'Profiling',
        'myPythonFile': 'c14_profiling.py',
        'myTitoCommand': 'tito module complete 14', 
        'myTFJSFile': None,
        'myDescription': 'Identifying performance bottlenecks in your code.',
        'myColor': '#2196f3'
    },
    15: {
        'myTitle': 'Quantization',
        'myPythonFile': 'c15_quantization.py',
        'myTitoCommand': 'tito module complete 15', 
        'myTFJSFile': None,
        'myDescription': 'Reducing model size and latency by lowering precision.',
        'myColor': '#009688'
    },
    16: {
        'myTitle': 'Compression',
        'myPythonFile': 'c16_compression.py',
        'myTitoCommand': 'tito module complete 16', 
        'myTFJSFile': None,
        'myDescription': 'Techniques like pruning and weight sharing for smaller models.',
        'myColor': '#e91e63'
    },
    17: {
        'myTitle': 'Memoization',
        'myPythonFile': 'c17_memoization.py',
        'myTitoCommand': 'tito module complete 17', 
        'myTFJSFile': None,
        'myDescription': 'Caching function results to avoid re-computation.',
        'myColor': '#ff5722'
    },
    18: {
        'myTitle': 'Acceleration',
        'myPythonFile': 'c18_acceleration.py',
        'myTitoCommand': 'tito module complete 18', 
        'myTFJSFile': None,
        'myDescription': 'Leveraging hardware (e.g., SIMD, GPU concepts) for speedup.',
        'myColor': '#9e9e9e'
    },
    19: {
        'myTitle': 'Benchmarking',
        'myPythonFile': 'c19_benchmarking.py',
        'myTitoCommand': 'tito module complete 19', 
        'myTFJSFile': None,
        'myDescription': 'Systematically comparing performance of different implementations.',
        'myColor': '#607d8b'
    },

    # 🏅 Capstone Competition
    20: {
        'myTitle': 'Torch Olympics',
        'myPythonFile': 'c20_olympics.py',
        'myTitoCommand': 'tito module complete 20', 
        'myTFJSFile': None,
        'myDescription': 'The final competition using all implemented concepts.',
        'myColor': '#ffeb3b' # Bright gold color
    },
}

# --- Helper Function for tito Execution ---

def my_execute_tito_command(myTitoCommand: str) -> str:
    """Executes a tito command in the shell and captures output."""
    myCommandParts = myTitoCommand.split()
    
    try:
        # Use subprocess.run to execute the command, capturing output and errors.
        myResult = subprocess.run(
            myCommandParts, 
            cwd='/usr/src/TinyTorch', # Crucial: run from the setup directory
            capture_output=True, 
            text=True, 
            check=True, # Raise an error for non-zero exit codes (though tito often uses return code 1 for test failures)
            shell=False,
            timeout=30 # Add a timeout to prevent hanging tests
        )
        # Return stdout (tito's successful output/results)
        return f"<pre style='color: green; font-weight: bold;'>✅ TITO Command Success (Exit 0):\n{myResult.stdout}</pre>"
        
    except subprocess.CalledProcessError as e:
        # Handle tito failing (e.g., tests failed, module not complete)
        # Display both stdout (test results) and stderr (potential errors)
        myErrorMessage = f"<pre style='color: red; font-weight: bold;'>❌ TITO Test Failure (Exit {e.returncode}):\n--- STDOUT ---\n{e.stdout}\n--- STDERR ---\n{e.stderr}</pre>"
        return myErrorMessage
        
    except FileNotFoundError:
        return "<pre style='color: red; font-weight: bold;'>CRITICAL ERROR: 'tito' command not found. Docker setup failed.</pre>"
    except subprocess.TimeoutExpired:
        return "<pre style='color: red; font-weight: bold;'>CRITICAL ERROR: TITO command timed out after 30 seconds.</pre>"
    except Exception as e:
        return f"<pre style='color: red;'>CRITICAL ERROR: Unexpected execution error: {type(e).__name__}: {e}</pre>"


# ----------------------------------------------------------------------
# 2. HOMEPAGE ROUTE - Lists Chapters
# ----------------------------------------------------------------------

@myApp.route('/', methods=['GET', 'POST'])
def my_homepage():
    """Renders the course homepage with a list of modules, linking to the overview page."""
    # Logic for Magic Word (Kept for continuity)
    myCheckResult = "<span style='color:red'> Try the magic word 'fred'</span>"
    
    if request.method == 'POST':
        myInputText = request.form.get('myText01')
        if myInputText == myMagicWord:
            myCheckResult = "<b style='color:green'> Cool! The magic word works! </b>"
        else:
            myCheckResult = "<span style='color:red'> Try the magic word 'fred'</span>"

    # Dynamic Module List Generation
    myModuleListHTML = ""
    
    # Define tier headings based on module ID ranges
    Tiers = {
        1: "🏗 Foundation Tier (01-07)",
        8: "🏛️ Architecture Tier (08-13)",
        14: "⏱️ Optimization Tier (14-19)",
        20: "🏅 Capstone Competition"
    }

    # Iterate through sorted keys and insert tier headers
    for myKey in sorted(COURSE_MODULES.keys()):
        myModule = COURSE_MODULES[myKey]
        
        # Check if a new tier should start
        if myKey in Tiers:
             myModuleListHTML += f"""
             <li style='margin-top: 25px; margin-bottom: 10px; font-size: 1.4em; font-weight: bold; color: #4a4a4a; border-bottom: 2px dashed #ddd; padding-bottom: 5px;'>
                {Tiers[myKey]}
             </li>
             """

        # Link directly to the Chapter Overview page
        myOverviewLink = url_for('my_chapter_overview', myModuleId=myKey)
        
        # Ensure chapter number is always two digits (e.g., 01, 10, 20)
        myChapterNumber = f"{myKey:02d}"

        myModuleListHTML += f"""
        <li style='margin-bottom: 15px; background-color: #f8f8ff; padding: 15px; border-left: 5px solid {myModule['myColor']}; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);'>
            <div style='font-size: 1.2em; font-weight: bold; color: #333;'>
                <a href='{myOverviewLink}' style='text-decoration: none; color: {myModule['myColor']};'>
                    Chapter {myChapterNumber}: {myModule['myTitle']}
                </a>
            </div>
            <p style='margin: 5px 0 10px 0; color: #6c757d; font-size: 0.9em;'>
                {myModule['myDescription']}
            </p>
        </li>
        """

    # HTML for the main page
    my_html_content = f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>TinyTorch Course Modules</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style='font-family: Arial, sans-serif; margin: 20px; padding: 0; background-color: #f4f4f9;'>
        <div id='myContainer' style='max-width: 700px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);'>
            <h1 style='text-align: center; color: #1a1a1a; margin-bottom: 5px;'>📚 TinyTorch Dual-Path Builder</h1>
            <p style='text-align: center; color: #6c757d;'>Select a chapter to see the assignment options (Python TITO Check / TFJS).</p>

            <h3 style='color: #4a4a4a; border-bottom: 2px solid #ddd; padding-bottom: 8px; margin-top: 25px;'>Course Modules</h3>
            <ul style='list-style-type: none; padding: 0;'>
                {myModuleListHTML}
            </ul>

            <hr style='margin: 30px 0; border-top: 1px solid #eee;'>

            <h4 style='text-align: center; color: #4a4a4a;'>Verification Check</h4>
            <form action="/" method="post" style='text-align: center; padding: 10px 0; margin-bottom: 0;'>
                <label for="myText01" style='font-weight: bold;'>Enter Magic Word:</label>
                <input type="text" id="myText01" name="myText01" value="" style='padding: 10px; margin: 0 10px; border: 1px solid #ced4da; border-radius: 6px; width: 40%; box-sizing: border-box;'>
                <input type="submit" value="Check" style='padding: 10px 20px; background-color: #333; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;'>
            </form>
            
            <div style="text-align: center; margin-top: 15px; font-weight: bold;">
                {myCheckResult}
            </div>
        </div>
    </body>
    </html>
    """
    return render_template_string(my_html_content)


# ----------------------------------------------------------------------
# 3. CHAPTER OVERVIEW ROUTE (The intermediate page)
# ----------------------------------------------------------------------

@myApp.route('/module<int:myModuleId>', methods=['GET'])
def my_chapter_overview(myModuleId: int):
    """Shows the overview page with Python and TFJS links."""
    myModule = COURSE_MODULES.get(myModuleId)
    if not myModule:
        return render_template_string(f"<h1>Module {myModuleId} Not Found</h1><a href='/'>Back</a>")

    myTitle = myModule['myTitle']
    myDescription = myModule['myDescription']
    myPythonFile = myModule['myPythonFile']
    myColor = myModule['myColor']
    myChapterNumber = f"{myModuleId:02d}"
    
    myPythonRoute = url_for('my_run_tito_check', myModuleId=myModuleId)
    myTFJSLinkHTML = ""

    if myModule.get('myTFJSFile'):
        # NOTE: For TFJS, we use a simple link to the static file route
        myTFJSRoute = url_for('my_chapter_tfjs_assignment', myModuleId=myModuleId)
        myTFJSLinkHTML = f"""
            <a href='{myTFJSRoute}' style='display: inline-block; padding: 15px 30px; background-color: #FF0090; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; transition: background-color 0.3s; margin-left: 15px;'>
                🦄 Go to TFJS Assignment ({myModule['myTFJSFile']})
            </a>
        """
    else:
        myTFJSLinkHTML = f"""
            <span style='display: inline-block; padding: 15px 30px; background-color: #ccc; color: #666; border-radius: 8px; font-weight: bold; cursor: not-allowed; margin-left: 15px;'>
                TFJS Assignment N/A
            </span>
        """

    my_html_content = f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>Chapter {myChapterNumber}: {myTitle} Overview</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style='font-family: Arial, sans-serif; margin: 20px; padding: 0; background-color: #f4f4f9;'>
        <div id='myContainer' style='max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1); border-top: 5px solid {myColor};'>
            <a href="/" style='display: inline-block; margin-bottom: 20px; text-decoration: none; color: #007bff; font-weight: bold;'>&larr; Back to Course Modules</a>
            
            <h1 style='text-align: center; color: #333; margin-top: 0;'>Chapter {myChapterNumber}: {myTitle}</h1>
            <p style='text-align: center; color: #6c757d; font-size: 1.1em;'>{myDescription}</p>

            <hr style='margin: 30px 0;'>

            <h3 style='color: #4a4a4a;'>Assignment Options:</h3>
            
            <div style='text-align: center; margin-top: 30px;'>
                <a href='{myPythonRoute}' style='display: inline-block; padding: 15px 30px; background-color: {myColor}; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; transition: background-color 0.3s;'>
                    🐍 Run Python TITO Check ({myPythonFile})
                </a>
                {myTFJSLinkHTML}
            </div>

            <p style='margin-top: 30px; text-align: center; font-style: italic; color: #888;'>
                The Python check executes the official <code>{myModule.get('myTitoCommand', 'tito module complete XX')}</code> command in the Docker environment.
            </p>

        </div>
    </body>
    </html>
    """
    return render_template_string(my_html_content)


# ----------------------------------------------------------------------
# 4. PYTHON TITO EXECUTION ROUTE (The target page for the Python button)
# ----------------------------------------------------------------------

@myApp.route('/module<int:myModuleId>/python', methods=['GET'])
def my_run_tito_check(myModuleId: int):
    """Executes the tito command and shows the result."""
    myModule = COURSE_MODULES.get(myModuleId)
    if not myModule or not myModule.get('myTitoCommand'):
        return redirect(url_for('my_chapter_overview', myModuleId=myModuleId))

    myTitoCommand = myModule['myTitoCommand']
    myTitle = myModule['myTitle']
    myColor = myModule['myColor']
    myChapterNumber = f"{myModuleId:02d}"
    
    # Execute the tito command for this module
    myTestResults = my_execute_tito_command(myTitoCommand)

    my_html_content = f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>TinyTorch C{myChapterNumber}: {myTitle} TITO Results</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style='font-family: Arial, sans-serif; margin: 20px; padding: 0; background-color: #f4f4f9;'>
        <div id='myContainer' style='max-width: 900px; margin: 0 auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1); border-top: 5px solid {myColor};'>
            <a href="{url_for('my_chapter_overview', myModuleId=myModuleId)}" style='display: inline-block; margin-bottom: 20px; text-decoration: none; color: #007bff; font-weight: bold;'>&larr; Back to Chapter Overview</a>
            
            <h1 style='text-align: center; color: #333;'>Python TITO Check Results</h1>
            <h3 style='text-align: center; color: {myColor}; margin-top: 0;'>Chapter {myChapterNumber}: {myTitle}</h3>

            <p style='text-align: center; font-style: italic; color: #555;'>Command executed: <code>{myTitoCommand}</code> (This may take a moment to run in the container)</p>
            
            <h4 style='color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px;'>TITO Output:</h4>
            <div id='myResultsBox' style='background-color: #e9ecef; padding: 15px; border-radius: 4px; border: 1px solid #ced4da; margin-top: 10px; word-wrap: break-word; overflow-x: auto;'>
                {myTestResults}
            </div>
        </div>
    </body>
    </html>
    """
    return render_template_string(my_html_content)


# ----------------------------------------------------------------------
# 5. TFJS ASSIGNMENT ROUTE (The target page for the TFJS button)
# ----------------------------------------------------------------------

@myApp.route('/module<int:myModuleId>/tfjs', methods=['GET'])
def my_chapter_tfjs_assignment(myModuleId: int):
    """Serves the self-contained HTML/JS environment for the TFJS assignment."""
    myModule = COURSE_MODULES.get(myModuleId)
    myChapterNumber = f"{myModuleId:02d}"

    if not myModule or not myModule.get('myTFJSFile'):
        return redirect(url_for('my_chapter_overview', myModuleId=myModuleId))

    myTFJSFileName = myModule['myTFJSFile']

    try:
        # Load the content of the corresponding static TFJS file
        # NOTE: In the Canvas environment, files are usually accessible by Flask
        with open(myTFJSFileName, 'r') as f:
            html_content = f.read()
    except FileNotFoundError:
        html_content = f"""
        <!DOCTYPE html>
        <html>
        <head>
            <title>TFJS Assignment C{myChapterNumber} Missing</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {{ font-family: Arial, sans-serif; background-color: #f4f4f9; text-align: center; padding-top: 50px; }}
                .message {{ max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }}
                a {{ color: #007bff; text-decoration: none; font-weight: bold; }}
            </style>
        </head>
        <body>
            <div class="message">
                <h1>TFJS Assignment Environment</h1>
                <p>The <strong>{myTFJSFileName}</strong> file for Chapter {myChapterNumber} needs to be created or uploaded to run this assignment.</p>
                <a href="{url_for('my_chapter_overview', myModuleId=myModuleId)}">Back to Overview</a>
            </div>
        </body>
        </html>
        """
    
    return html_content
