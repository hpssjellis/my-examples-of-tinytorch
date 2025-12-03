from flask import Flask, request, render_template_string
import importlib.util
import io
import sys
import numpy as np # Used for internal testing of the student's Tensor class

# Following user preference: Descriptive my* camelCase variable names
myApp = Flask(__name__) 
myMagicWord = 'fred'

# ----------------------------------------------------------------------
# Module 1: Tensors - Assignment Route
# ----------------------------------------------------------------------

@myApp.route('/module1', methods=['GET'])
def my_chapter_1_tensor():
    """Renders the Module 1 assignment page and runs live tests on my_tensor.py."""
    myTestResults = ""
    myModuleName = 'my_tensor'
    
    # 1. Dynamic Import and Test Setup (Similar to shell_exec, but faster and safer)
    mySpec = importlib.util.find_spec(myModuleName)
    
    if mySpec is None:
        myTestResults = f"<p style='color: red; font-weight: bold;'>ERROR: Could not find '{myModuleName}.py'. Please create this file for Module 1.</p>"
    else:
        # 2. Capture print output from the tests
        myOutputCapture = io.StringIO()
        myOldStdout = sys.stdout # Save old stdout
        sys.stdout = myOutputCapture
        
        try:
            # Dynamically import the student's module
            myModule = importlib.util.module_from_spec(mySpec)
            sys.modules[myModuleName] = myModule
            mySpec.loader.exec_module(myModule)
            
            if not hasattr(myModule, 'Tensor'):
                raise AttributeError("The 'my_tensor.py' file must contain a class named 'Tensor'.")
            
            myTensorClass = myModule.Tensor
            
            # --- Live Assignment Tests (Run on the student's code) ---
            print("--- Running TinyTorch Module 1 Tests ---")
            
            # Test 1: Creation and Shape
            myInputData = np.array([[1.0, 2.0], [3.0, 4.0]])
            myT1 = myTensorClass(myInputData)
            
            # Try to get the shape property, falling back to data.shape if 'data' is a property
            myShape = getattr(myT1, 'shape', 'N/A')
            if myShape == 'N/A' and hasattr(myT1, 'data'):
                myShape = myT1.data.shape
                
            print(f"✅ Test 1: Tensor Created. Shape: {myShape}")
            
            # Test 2: Addition (__add__ implementation)
            myT_add = myT1 + myT1
            myAddData = getattr(myT_add, 'data', None)
            
            if myAddData is not None and np.array_equal(myAddData, myInputData * 2):
                print(f"✅ Test 2: Addition Successful. Result data is correct.")
            else:
                print(f"❌ Test 2: Addition Failed. Expected a result of [[2.0, 4.0], [6.0, 8.0]]. Check your __add__ method.")
            
            myTestResults = myOutputCapture.getvalue()
            
        except Exception as e:
            # Capture any runtime or import errors
            myTestResults = f"CRITICAL TEST ERROR:\n{e}"
            
        finally:
            sys.stdout = myOldStdout # Restore stdout
            
    # 3. HTML Rendering with simple, inline CSS
    my_html_content = f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>TinyTorch Module 1: Tensor</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style='font-family: Arial, sans-serif; margin: 20px; padding: 0; background-color: #f4f4f9;'>
        <div id='myContainer' style='max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
            <a href="/" style='display: inline-block; margin-bottom: 20px; text-decoration: none; color: #007bff; font-weight: bold;'>&larr; Back to Course Modules</a>

            <h3 style='text-align: center; color: #333;'>Module 1: Tensors - The Building Blocks 🧱</h3>
            <p>Your task is to implement the core <code>Tensor</code> class in the file <code>my_tensor.py</code>. This object must act as a multi-dimensional array.</p>
            
            <h4 style='color: #007bff;'>Assignment Requirements:</h4>
            <ul style='list-style-type: disc; margin-left: 20px;'>
                <li>Define a class named <code>Tensor</code> in <code>my_tensor.py</code>.</li>
                <li>The <code>__init__</code> method should accept data and store it, typically wrapping a **NumPy array** (which is installed).</li>
                <li>Implement basic math operations, starting with **addition** (using the Python method <code>__add__</code>).</li>
                <li>Ensure the class is importable and can be instantiated without errors.</li>
            </ul>

            <h4 style='text-align: center; color: #333;'>Live Test Results for <code>my_tensor.py</code></h4>
            <div id='myResultsBox' style='background-color: #e9ecef; padding: 15px; border-radius: 4px; border: 1px solid #ced4da; margin-top: 20px; word-wrap: break-word;'>
                <pre style='white-space: pre-wrap; margin: 0;'>{myTestResults}</pre>
            </div>
        </div>
    </body>
    </html>
    """
    return render_template_string(my_html_content)

# ----------------------------------------------------------------------
# Homepage Route - Shows Chapter List
# ----------------------------------------------------------------------

@myApp.route('/', methods=['GET', 'POST'])
def my_homepage():
    """Renders the course homepage with a list of modules and the magic word check."""
    # Logic for Magic Word (reusing the original functionality)
    myCheckResult = "<span style='color:red'> Try the magic word 'fred'</span>"
    
    if request.method == 'POST':
        myInputText = request.form.get('myText01')
        
        if myInputText == myMagicWord:
            myCheckResult = "<b style='color:green'> Cool! The magic word works! </b>"
        else:
            myCheckResult = "<span style='color:red'> Try the magic word 'fred'</span>"

    # HTML for the main page (Chapters List)
    my_html_content = f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>TinyTorch Course Modules</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style='font-family: Arial, sans-serif; margin: 20px; padding: 0; background-color: #f4f4f9;'>
        <div id='myContainer' style='max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
            <h1 style='text-align: center; color: #333;'>📚 TinyTorch - Build Your Own ML Framework</h1>
            
            <h3 style='color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 5px;'>Course Modules</h3>
            <ul style='list-style-type: none; padding: 0;'>
                <li style='margin-bottom: 10px; background-color: #f0f8ff; padding: 10px; border-left: 5px solid #007bff; border-radius: 4px;'>
                    <b style='color: #007bff;'>✅ Module 1: Tensors</b>
                    <p style='margin: 5px 0 0 0;'>Implement the core multi-dimensional array data structure.</p>
                    <a href="/module1" style='color: #28a745; font-weight: bold; text-decoration: none; display: block; margin-top: 5px;'>&rarr; Start Assignment</a>
                </li>
                <li style='margin-bottom: 10px; background-color: #f8f9fa; padding: 10px; border-left: 5px solid #6c757d; border-radius: 4px;'>
                    <b>Module 2: Autograd</b> (Coming Soon)
                    <p style='margin: 5px 0 0 0; color: #6c757d;'>Implement automatic differentiation and the backward pass.</p>
                </li>
                <li style='margin-bottom: 10px; background-color: #f8f9fa; padding: 10px; border-left: 5px solid #6c757d; border-radius: 4px;'>
                    <b>Module 3: Optimizers</b> (Coming Soon)
                    <p style='margin: 5px 0 0 0; color: #6c757d;'>Implement Stochastic Gradient Descent (SGD) and Adam.</p>
                </li>
            </ul>

            <hr style='margin: 20px 0;'>

            <h4 style='text-align: center; color: #333;'>Verification Check</h4>
            <form action="/" method="post" style='text-align: center; padding: 10px 0; margin-bottom: 0; border: none;'>
                <label for="myText01">Enter Magic Word:</label>
                <input type="text" id="myText01" name="myText01" value="" style='padding: 8px; margin-right: 10px; border: 1px solid #ddd; border-radius: 4px; width: 40%;'>
                <input type="submit" value="Check" style='padding: 8px 15px; background-color: #333; color: white; border: none; border-radius: 4px; cursor: pointer;'>
            </form>
            
            <div style="text-align: center; margin-top: 10px;">
                {myCheckResult}
            </div>

        </div>
    </body>
    </html>
    """
    return render_template_string(my_html_content)

# Note: The Gunicorn setup on Render handles starting the server instance: myApp
