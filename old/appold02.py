# ----------------------------------------------------------------------
# Module 3: Optimizers - New Assignment Route
# ----------------------------------------------------------------------

@myApp.route('/module3', methods=['GET'])
def my_chapter_3_optimizers():
    """Renders the Module 3 assignment page and tests the Optimizer class."""
    myTestResults = ""
    myModuleName = 'my_tensor' # Still operating on my_tensor.py or possibly a new my_optimizer.py
    
    mySpec = importlib.util.find_spec(myModuleName)
    
    if mySpec is None:
        myTestResults = f"<p style='color: red; font-weight: bold;'>ERROR: Could not find '{myModuleName}.py'. Please ensure it exists.</p>"
    else:
        myOutputCapture = io.StringIO()
        myOldStdout = sys.stdout 
        sys.stdout = myOutputCapture
        
        try:
            myModule = importlib.util.module_from_spec(mySpec)
            sys.modules[myModuleName] = myModule
            mySpec.loader.exec_module(myModule)
            
            # This module typically requires a separate Optimizer class (e.g., SGD)
            if not hasattr(myModule, 'SGD'):
                 raise AttributeError("my_tensor.py must contain a class named 'SGD' (Stochastic Gradient Descent).")
            
            myTensorClass = myModule.Tensor
            mySGDClass = myModule.SGD
            
            # --- Live Assignment Tests (Optimizer) ---
            print("--- Running TinyTorch Module 3 Tests (SGD Optimizer) ---")
            
            # Setup a simple tensor that requires a gradient update
            myWeight = myTensorClass([10.0])
            myWeight.grad = np.array([2.0], dtype=np.float32) # Assume a calculated gradient
            
            # 1. Initialize the optimizer
            myOptimizer = mySGDClass(myParams=[myWeight], myLearningRate=0.1)
            
            # 2. Perform one step
            myOptimizer.myZeroGrad() # Check if grad is reset
            myOptimizer.myStep()     # Check if data is updated
            
            # Test 1: Check if gradient was reset to zero by myZeroGrad()
            # We assume myZeroGrad is called *before* myStep, which is incorrect in this linear flow
            # Let's test the step logic directly for now, as myZeroGrad is often called after step for the next iter.
            
            # Re-initialize for a clean test of myStep()
            myWeight = myTensorClass([10.0])
            myWeight.grad = np.array([2.0], dtype=np.float32) 
            myOptimizer = mySGDClass(myParams=[myWeight], myLearningRate=0.1)
            myOptimizer.myStep() 
            
            # Expected new weight: 10.0 - (2.0 * 0.1) = 9.8
            myNewWeightValue = myWeight.data.sum()
            
            if np.isclose(myNewWeightValue, 9.8):
                print(f"✅ Test 1: SGD Step successful. Weight updated to {myNewWeightValue:.2f} (Expected 9.8).")
            else:
                print(f"❌ Test 1: SGD Step Failed. Found: {myNewWeightValue:.2f}. Expected: 9.8. Check your myStep() formula: w = w - lr * grad.")

            # Test 2: Check myZeroGrad() functionality
            myOptimizer.myZeroGrad() 
            myGradValue = myWeight.grad.sum()
            
            if np.isclose(myGradValue, 0.0):
                 print(f"✅ Test 2: myZeroGrad successful. Grad is {myGradValue:.1f}.")
            else:
                 print(f"❌ Test 2: myZeroGrad Failed. Grad is {myGradValue:.1f}. Expected 0.0.")

            myTestResults = myOutputCapture.getvalue()
            
        except AttributeError as e:
            myTestResults = f"MISSING FEATURE ERROR:\n{e}. Please ensure your my_tensor.py has all required methods/properties."
        except Exception as e:
            myTestResults = f"CRITICAL RUNTIME ERROR:\n{e}"
            
        finally:
            sys.stdout = myOldStdout
            
    # HTML Rendering
    my_html_content = f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>TinyTorch Module 3: Optimizers</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style='font-family: Arial, sans-serif; margin: 20px; padding: 0; background-color: #f4f4f9;'>
        <div id='myContainer' style='max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
            <a href="/" style='display: inline-block; margin-bottom: 20px; text-decoration: none; color: #007bff; font-weight: bold;'>&larr; Back to Course Modules</a>

            <h3 style='text-align: center; color: #333;'>Module 3: Optimizers - Teaching the Network 🧑‍🏫</h3>
            <p>Your task is to create the core **SGD (Stochastic Gradient Descent)** optimizer class to update the weights based on the computed gradients.</p>
            
            <h4 style='color: #007bff;'>Assignment Requirements (Update <code>my_tensor.py</code>):</h4>
            <ul style='list-style-type: disc; margin-left: 20px;'>
                <li>Create a new class named <code>SGD</code>.</li>
                <li>The <code>__init__</code> method must accept a list of parameters (Tensors) and a learning rate (<code>myLearningRate</code>).</li>
                <li>Implement **<code>myStep()</code>** to update the <code>data</code> of each parameter in the list using the formula: $w = w - \text{lr} \times \text{grad}$.</li>
                <li>Implement **<code>myZeroGrad()</code>** to reset the <code>grad</code> attribute of all parameters back to zero.</li>
            </ul>

            <h4 style='text-align: center; color: #333;'>Live Test Results (Simple SGD Step)</h4>
            <p style='text-align: center; font-style: italic; font-size: 0.9em;'>Testing if a weight of 10.0 with a gradient of 2.0 and learning rate of 0.1 updates correctly.</p>
            <div id='myResultsBox' style='background-color: #e9ecef; padding: 15px; border-radius: 4px; border: 1px solid #ced4da; margin-top: 20px; word-wrap: break-word;'>
                <pre style='white-space: pre-wrap; margin: 0;'>{myTestResults}</pre>
            </div>
        </div>
    </body>
    </html>
    """
    return render_template_string(my_html_content)
