from flask import Flask, request, render_template_string
import importlib.util
import io
import sys
import numpy as np 
from time import sleep

# Following user preference: Descriptive my* camelCase variable names
myApp = Flask(__name__) 
myMagicWord = 'fred'

# ----------------------------------------------------------------------
# Helper Functions for Dynamic Testing
# ----------------------------------------------------------------------

def my_import_module(myModuleName):
    """Dynamically imports the student's assignment file."""
    mySpec = importlib.util.find_spec(myModuleName)
    if mySpec is None:
        return None, f"Could not find '{myModuleName}.py'. Please create this file."
    
    # Capture old stdout/stderr to prevent student code side-effects from polluting console
    myOldStdout = sys.stdout
    myOldStderr = sys.stderr 
    
    try:
        myModule = importlib.util.module_from_spec(mySpec)
        sys.modules[myModuleName] = myModule
        mySpec.loader.exec_module(myModule)
        return myModule, None
    except Exception as e:
        return None, f"CRITICAL IMPORT ERROR in {myModuleName}.py:\n{type(e).__name__}: {e}"
    finally:
        # Restore stdout/stderr
        sys.stdout = myOldStdout
        sys.stderr = myOldStderr


# ----------------------------------------------------------------------
# Module 1: Tensors - Assignment Route
# ----------------------------------------------------------------------

@myApp.route('/module1', methods=['GET'])
def my_chapter_1_tensor():
    """Renders the Module 1 assignment page and runs live tests on c01_tensor.py."""
    myTestResults = ""
    myModuleName = 'c01_tensor'
    
    myModule, myError = my_import_module(myModuleName)
    
    if myError:
        myTestResults = f"<p style='color: red; font-weight: bold;'>ERROR: {myError}</p>"
    else:
        # 2. Capture print output from the tests
        myOutputCapture = io.StringIO()
        myOldStdout = sys.stdout 
        sys.stdout = myOutputCapture
        
        try:
            if not hasattr(myModule, 'Tensor'):
                raise AttributeError("The 'c01_tensor.py' file must contain a class named 'Tensor'.")
            
            myTensorClass = myModule.Tensor
            
            # --- Live Assignment Tests (Run on the student's code) ---
            print("--- Running TinyTorch Module 1 Tests (Tensors) ---")
            
            # Test 1: Creation and Shape
            myInputData = np.array([[1.0, 2.0], [3.0, 4.0]])
            myT1 = myTensorClass(myInputData)
            
            # Use standard 'shape' property
            myShape = getattr(myT1, 'shape', 'N/A')
            
            print(f"✅ Test 1: Tensor Created. Shape: {myShape}")
            
            # Test 2: Addition (__add__ implementation)
            myT_add = myT1 + myT1
            myAddData = getattr(myT_add, 'data', None)
            
            if myAddData is not None and np.array_equal(myAddData, myInputData * 2):
                print(f"✅ Test 2: Addition Successful. Result data is correct.")
            else:
                print(f"❌ Test 2: Addition Failed. Expected [[2.0, 4.0], [6.0, 8.0]]. Check your __add__ method and 'data' attribute.")
            
            myTestResults = myOutputCapture.getvalue()
            
        except Exception as e:
            myTestResults = f"CRITICAL TEST ERROR:\n{type(e).__name__}: {e}"
            
        finally:
            sys.stdout = myOldStdout # Restore stdout
            
    # HTML Rendering
    my_html_content = f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>TinyTorch C01: Tensors</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style='font-family: Arial, sans-serif; margin: 20px; padding: 0; background-color: #f4f4f9;'>
        <div id='myContainer' style='max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
            <a href="/" style='display: inline-block; margin-bottom: 20px; text-decoration: none; color: #007bff; font-weight: bold;'>&larr; Back to Course Modules</a>

            <h3 style='text-align: center; color: #333;'>Chapter 1: Tensors - The Building Blocks 🧱</h3>
            <p>Your task is to implement the core <code>Tensor</code> class in the file <code>c01_tensor.py</code>. This object must act as a multi-dimensional array.</p>
            
            <h4 style='color: #007bff;'>Assignment Requirements (In <code>c01_tensor.py</code>):</h4>
            <ul style='list-style-type: disc; margin-left: 20px;'>
                <li>Define a class named <code>Tensor</code>.</li>
                <li>The <code>__init__</code> method should store data in a property named <code>data</code> (NumPy array).</li>
                <li>Implement basic math operations, starting with **addition** (<code>__add__</code>).</li>
            </ul>

            <h4 style='text-align: center; color: #333;'>Live Test Results for <code>c01_tensor.py</code></h4>
            <div id='myResultsBox' style='background-color: #e9ecef; padding: 15px; border-radius: 4px; border: 1px solid #ced4da; margin-top: 20px; word-wrap: break-word;'>
                <pre style='white-space: pre-wrap; margin: 0;'>{myTestResults}</pre>
            </div>
        </div>
    </body>
    </html>
    """
    return render_template_string(my_html_content)


# ----------------------------------------------------------------------
# Module 2: Autograd - Assignment Route
# ----------------------------------------------------------------------

@myApp.route('/module2', methods=['GET'])
def my_chapter_2_autograd():
    """Renders the Module 2 assignment page and runs live tests on c01_tensor.py for autograd."""
    myTestResults = ""
    myModuleName = 'c01_tensor'
    
    myModule, myError = my_import_module(myModuleName)
    
    if myError:
        myTestResults = f"<p style='color: red; font-weight: bold;'>ERROR: {myError}</p>"
    else:
        # Capture print output
        myOutputCapture = io.StringIO()
        myOldStdout = sys.stdout 
        sys.stdout = myOutputCapture
        
        try:
            if not hasattr(myModule, 'Tensor'):
                raise AttributeError("The 'c01_tensor.py' file must contain a class named 'Tensor'.")
            
            myTensorClass = myModule.Tensor
            
            # --- Live Assignment Tests (Autograd) ---
            print("--- Running TinyTorch Module 2 Tests (Autograd) ---")
            
            # Simple scalar test: D = (A * B) + B
            myAData = np.array([2.0])
            myBData = np.array([3.0])
            
            # Check for requires_grad implementation (initial step for autograd)
            myT_test = myTensorClass(myAData)
            if not hasattr(myT_test, 'grad'):
                 raise AttributeError("Tensor class is missing the 'grad' attribute (initialized to None).")
            if not hasattr(myT_test, 'backward'):
                 raise AttributeError("Tensor class is missing the 'backward' method.")
            
            myA = myTensorClass(myAData)
            myB = myTensorClass(myBData)
            
            # The computation graph: c = (a * b) + b
            myC = myA * myB # Requires __mul__
            myD = myC + myB 
                 
            myD.backward()
            
            # Expected gradients: dD/dA = 3.0, dD/dB = 3.0
            
            # Test 1: Check grad on 'A' (Expected: 3.0)
            myAGrad = getattr(myA, 'grad', np.array([0.0]))
            if myAGrad is not None and np.isclose(myAGrad.sum(), 3.0):
                print(f"✅ Test 1: Gradient on 'A' is correct (Expected: 3.0).")
            else:
                print(f"❌ Test 1: Gradient on 'A' Failed. Found: {myAGrad.sum():.2f}. Expected: 3.0. Check your __mul__ backward pass.")

            # Test 2: Check grad on 'B' (Expected: 3.0)
            myBGrad = getattr(myB, 'grad', np.array([0.0]))
            if myBGrad is not None and np.isclose(myBGrad.sum(), 3.0):
                print(f"✅ Test 2: Gradient on 'B' is correct (Expected: 3.0).")
            else:
                print(f"❌ Test 2: Gradient on 'B' Failed. Found: {myBGrad.sum():.2f}. Expected: 3.0. Check your __mul__ and __add__ backward passes.")
                
            myTestResults = myOutputCapture.getvalue()
            
        except AttributeError as e:
            myTestResults = f"MISSING FEATURE ERROR:\n{e}. Please ensure your c01_tensor.py has all required methods/properties."
        except Exception as e:
            myTestResults = f"CRITICAL RUNTIME ERROR:\n{type(e).__name__}: {e}"
            
        finally:
            sys.stdout = myOldStdout # Restore stdout
            
    # HTML Rendering
    my_html_content = f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>TinyTorch C02: Autograd</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style='font-family: Arial, sans-serif; margin: 20px; padding: 0; background-color: #f4f4f9;'>
        <div id='myContainer' style='max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
            <a href="/" style='display: inline-block; margin-bottom: 20px; text-decoration: none; color: #007bff; font-weight: bold;'>&larr; Back to Course Modules</a>

            <h3 style='text-align: center; color: #333;'>Chapter 2: Autograd - Automatic Differentiation 🧬</h3>
            <p>Your task is to update the <code>Tensor</code> class in <code>c01_tensor.py</code> to track the computation graph and compute gradients backward.</p>
            
            <h4 style='color: #007bff;'>Assignment Requirements (Update <code>c01_tensor.py</code>):</h4>
            <ul style='list-style-type: disc; margin-left: 20px;'>
                <li>Update <code>__init__</code> to track parents (<code>_prev</code>) and initialize <code>grad</code>.</li>
                <li>Implement the **multiplication** operator (<code>__mul__</code>) and define its local derivative function (<code>_backward</code>).</li>
                <li>Implement the core **<code>backward()</code>** method, which performs the topological sort and calls `_backward`.</li>
            </ul>

            <h4 style='text-align: center; color: #333;'>Live Test Results (Simple Chain Rule)</h4>
            <p style='text-align: center; font-style: italic; font-size: 0.9em;'>Testing $D = (A \\times B) + B$ for $A=2.0$ and $B=3.0$.</p>
            <div id='myResultsBox' style='background-color: #e9ecef; padding: 15px; border-radius: 4px; border: 1px solid #ced4da; margin-top: 20px; word-wrap: break-word;'>
                <pre style='white-space: pre-wrap; margin: 0;'>{myTestResults}</pre>
            </div>
        </div>
    </body>
    </html>
    """
    return render_template_string(my_html_content)


# ----------------------------------------------------------------------
# Module 3: Optimizers - Assignment Route
# ----------------------------------------------------------------------

@myApp.route('/module3', methods=['GET'])
def my_chapter_3_optimizers():
    """Renders the Module 3 assignment page and tests the SGD Optimizer in c02_optimizer.py."""
    myTestResults = ""
    myModuleName = 'c02_optimizer'
    
    # 1. First, check for the Optimizer file itself
    myModule, myError = my_import_module(myModuleName)
    
    if myError:
        myTestResults = f"<p style='color: red; font-weight: bold;'>ERROR: {myError}</p>"
    else:
        # 2. Check for the dependency (c01_tensor.py)
        myTensorModule, myTensorError = my_import_module('c01_tensor')
        if myTensorError:
            myTestResults = f"<p style='color: red; font-weight: bold;'>DEPENDENCY ERROR: {myTensorError}</p>"
        elif not hasattr(myTensorModule, 'Tensor'):
            myTestResults = f"<p style='color: red; font-weight: bold;'>DEPENDENCY ERROR: c01_tensor.py must contain the Tensor class.</p>"
        else:
            myTensorClass = myTensorModule.Tensor
            
            # 3. Capture print output and run tests
            myOutputCapture = io.StringIO()
            myOldStdout = sys.stdout 
            sys.stdout = myOutputCapture
            
            try:
                if not hasattr(myModule, 'SGD'):
                     raise AttributeError("c02_optimizer.py must contain a class named 'SGD' (Stochastic Gradient Descent).")
                
                mySGDClass = myModule.SGD
                
                # --- Live Assignment Tests (Optimizer) ---
                print("--- Running TinyTorch Module 3 Tests (SGD Optimizer) ---")
                
                # Setup a simple tensor that requires a gradient update
                myWeight = myTensorClass([10.0])
                myWeight.grad = np.array([2.0], dtype=np.float32) # Assume a calculated gradient
                
                # Test 1: Check myStep() functionality
                myOptimizer = mySGDClass(myParams=[myWeight], myLearningRate=0.1)
                myOptimizer.myStep()     
                
                # Expected new weight: 10.0 - (2.0 * 0.1) = 9.8
                myNewWeightValue = myWeight.data.sum()
                
                if np.isclose(myNewWeightValue, 9.8):
                    print(f"✅ Test 1: SGD Step successful. Weight updated to {myNewWeightValue:.2f} (Expected 9.8).")
                else:
                    print(f"❌ Test 1: SGD Step Failed. Found: {myNewWeightValue:.2f}. Expected: 9.8. Check your myStep() formula.")
    
                # Test 2: Check myZeroGrad() functionality
                myWeight.grad = np.array([5.0], dtype=np.float32) # Reset grad for test
                myOptimizer.myZeroGrad() 
                myGradValue = myWeight.grad.sum()
                
                if np.isclose(myGradValue, 0.0):
                     print(f"✅ Test 2: myZeroGrad successful. Grad is {myGradValue:.1f}.")
                else:
                     print(f"❌ Test 2: myZeroGrad Failed. Grad is {myGradValue:.1f}. Expected 0.0.")

                myTestResults = myOutputCapture.getvalue()
                
            except AttributeError as e:
                myTestResults = f"MISSING FEATURE ERROR:\n{e}. Check if you created the SGD class and its methods correctly."
            except Exception as e:
                myTestResults = f"CRITICAL RUNTIME ERROR:\n{type(e).__name__}: {e}"
                
            finally:
                sys.stdout = myOldStdout
            
    # HTML Rendering
    my_html_content = f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>TinyTorch C03: Optimizers</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style='font-family: Arial, sans-serif; margin: 20px; padding: 0; background-color: #f4f4f9;'>
        <div id='myContainer' style='max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
            <a href="/" style='display: inline-block; margin-bottom: 20px; text-decoration: none; color: #007bff; font-weight: bold;'>&larr; Back to Course Modules</a>

            <h3 style='text-align: center; color: #333;'>Chapter 3: Optimizers - Teaching the Network 🧑‍🏫</h3>
            <p>Your task is to create the core **SGD (Stochastic Gradient Descent)** optimizer class in the new file <code>c02_optimizer.py</code>.</p>
            
            <h4 style='color: #007bff;'>Assignment Requirements (In <code>c02_optimizer.py</code>):</h4>
            <ul style='list-style-type: disc; margin-left: 20px;'>
                <li>Create a new class named <code>SGD</code>.</li>
                <li>The <code>__init__</code> method must accept a list of Tensors (<code>myParams</code>) and a learning rate (<code>myLearningRate</code>).</li>
                <li>Implement **<code>myStep()</code>** to update the <code>data</code> of each parameter: $w = w - \text{lr} \times \text{grad}$.</li>
                <li>Implement **<code>myZeroGrad()</code>** to reset the <code>grad</code> attribute of all parameters to zero.</li>
            </ul>

            <h4 style='text-align: center; color: #333;'>Live Test Results (Simple SGD Step)</h4>
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
    # Logic for Magic Word
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
                    <b style='color: #007bff;'>Chapter 1: Tensors</b>
                    <p style='margin: 5px 0 0 0;'>File: <code>c01_tensor.py</code></p>
                    <a href="/module1" style='color: #007bff; font-weight: bold; text-decoration: none; display: block; margin-top: 5px;'>&rarr; View Assignment Check (Tensors)</a>
                </li>
                <li style='margin-bottom: 10px; background-color: #e6ffed; padding: 10px; border-left: 5px solid #28a745; border-radius: 4px;'>
                    <b style='color: #28a745;'>Chapter 2: Autograd</b> 
                    <p style='margin: 5px 0 0 0;'>File: <code>c01_tensor.py</code> (Update)</p>
                    <a href="/module2" style='color: #28a745; font-weight: bold; text-decoration: none; display: block; margin-top: 5px;'>&rarr; View Assignment Check (Autograd)</a>
                </li>
                <li style='margin-bottom: 10px; background-color: #fffae6; padding: 10px; border-left: 5px solid #ffc107; border-radius: 4px;'>
                    <b style='color: #ffc107;'>Chapter 3: Optimizers (SGD)</b> 
                    <p style='margin: 5px 0 0 0; color: #6c757d;'>File: <code>c02_optimizer.py</code></p>
                    <a href="/module3" style='color: #ffc107; font-weight: bold; text-decoration: none; display: block; margin-top: 5px;'>&rarr; View Assignment Check (Optimizers)</a>
                </li>
                <li style='margin-bottom: 10px; background-color: #f8f9fa; padding: 10px; border-left: 5px solid #6c757d; border-radius: 4px;'>
                    <b>Chapter 4: Linear Layer</b> 
                    <p style='margin: 5px 0 0 0; color: #6c757d;'>File: <code>c03_linear_layer.py</code> (Coming Soon)</p>
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
