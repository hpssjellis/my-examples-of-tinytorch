
<?php
// Initialize variables for the result message and the input text.
$checkResult = "<span style='color:red'> Try the magic word 'fred' ....</span>";
$myInputText01 = '';
$tinyTorchOutput = '';
$tensorDemo = '';
$titoVersion = '';

// --- Shell Command Execution for Environment Check ---

// Helper function to safely execute a command and return output or an error message
function runCommandCheck(string $command): string {
    $output = shell_exec($command . ' 2>&1');
    if ($output === null) {
        return "Command failed or not found: $command";
    }
    $trimmedOutput = trim($output);
    if (empty($trimmedOutput)) {
        return "Command executed, but returned no visible output.";
    }
    return $trimmedOutput;
}

// Check Node.js version
$nodeVersion = runCommandCheck('node --version');

// Check Python version
$pythonVersion = runCommandCheck('python --version');

// Check TinyTorch installation (handle incomplete modules gracefully)
$titoCheck = shell_exec('python -c "import sys; sys.path.insert(0, \'/TinyTorch\'); import os; print(\'TinyTorch found at:\', \'/TinyTorch\' if os.path.exists(\'/TinyTorch/tinytorch\') else \'Not found\')" 2>&1');
$titoVersion = trim($titoCheck) ?: "TinyTorch directory check failed";

// --- End of Shell Command Execution ---

// Check if the form was submitted (i.e., if the request method is POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input text from the form data.
    $myInputText01 = $_POST['myText01'] ?? '';
    
    // Check which button was pressed
    $action = $_POST['action'] ?? 'check';

    // The magic word logic
    if ($action === 'check') {
        $myCheck = false;
        if ($myInputText01 === 'fred') {
            $myCheck = true;
        }

        // Determine the result message and color
        if ($myCheck) {
            $checkResult = "<b style='color:green'> Cool! The magic word works! </b>";
        } else {
            $checkResult = "<span style='color:red'> Try the magic word 'fred' ....</span>";
        }
    }
    // TinyTorch Tensor Demo
    if ($action === 'tensor_demo') {
        $pythonScript = <<<PYTHON
import sys
sys.path.insert(0, '/TinyTorch')
from tinytorch import Tensor
import numpy as np

# Create some tensors
print("=== TinyTorch Tensor Demo ===")
print()

# Example 1: Simple tensor creation
t1 = Tensor([1, 2, 3, 4, 5])
print(f"Tensor 1: {t1}")
print(f"Shape: {t1.shape}")
print(f"Data type: {type(t1.data)}")
print()

# Example 2: Tensor operations
t2 = Tensor([2, 2, 2, 2, 2])
t3 = t1 + t2
print(f"Tensor 2: {t2}")
print(f"Addition (t1 + t2): {t3}")
print()

# Example 3: Matrix tensor
matrix = Tensor([[1, 2, 3], [4, 5, 6]])
print(f"Matrix Tensor: {matrix}")
print(f"Matrix Shape: {matrix.shape}")
print()

# Example 4: Tensor with requires_grad
t4 = Tensor([1.0, 2.0, 3.0], requires_grad=True)
print(f"Tensor with gradient tracking: {t4}")
print(f"Requires grad: {t4.requires_grad}")
print()

print("=== Demo Complete ===")
PYTHON;

        // Write the Python script to a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'tinytorch_') . '.py';
        file_put_contents($tempFile, $pythonScript);
        
        // Execute the Python script
        $tensorDemo = shell_exec("python $tempFile 2>&1");
        
        // Clean up
        unlink($tempFile);
        
        $checkResult = "<b style='color:green'>TinyTorch Tensor Demo Executed!</b>";
    }
    
    // Run a simple TinyTorch import test
    if ($action === 'import_test') {
        $importTest = <<<PYTHON
import sys
sys.path.insert(0, '/TinyTorch')

print("=== TinyTorch Installation Check ===")
print()

# Check if TinyTorch directory exists
import os
tito_path = '/TinyTorch'
if os.path.exists(tito_path):
    print(f"✓ TinyTorch directory found at: {tito_path}")
else:
    print(f"✗ TinyTorch directory not found at: {tito_path}")
    sys.exit(1)

print()

# Check for tinytorch package
tinytorch_pkg = os.path.join(tito_path, 'tinytorch')
if os.path.exists(tinytorch_pkg):
    print(f"✓ tinytorch package found at: {tinytorch_pkg}")
else:
    print(f"✗ tinytorch package not found")
    sys.exit(1)

print()

# Try to import the base package
try:
    import tinytorch
    print("✓ tinytorch base package can be imported")
    print(f"  Package location: {tinytorch.__file__}")
except ImportError as e:
    print(f"✗ Cannot import tinytorch: {e}")
    sys.exit(1)

print()

# List what's in the tinytorch directory
print("TinyTorch package contents:")
tinytorch_contents = os.listdir(tinytorch_pkg)
for item in sorted(tinytorch_contents):
    item_path = os.path.join(tinytorch_pkg, item)
    if os.path.isdir(item_path) and not item.startswith('__'):
        print(f"  📁 {item}/")
    elif item.endswith('.py') and not item.startswith('__'):
        print(f"  📄 {item}")

print()

# Check if modules need to be completed
print("Note: TinyTorch is an educational framework.")
print("Some modules may need to be completed using 'tito module complete <number>'")
print()

# Try importing Tensor (the core component)
try:
    from tinytorch import Tensor
    print("✓ Tensor class is available!")
    print("  You can create tensors: Tensor([1, 2, 3])")
except ImportError as e:
    print(f"⚠ Tensor import note: {e}")
    print("  This is normal - complete the tensor module first.")

print()
print("=== Installation Check Complete ===")
PYTHON;

        $tempFile = tempnam(sys_get_temp_dir(), 'tinytorch_import_') . '.py';
        file_put_contents($tempFile, $importTest);
        $tinyTorchOutput = shell_exec("python $tempFile 2>&1");
        unlink($tempFile);
        
        $checkResult = "<b style='color:blue'>TinyTorch Installation Check Complete!</b>";
    }
    
    // Run tito module start command
    if ($action === 'tito_start') {
        $moduleNumber = $_POST['module_number'] ?? '01';
        
        $titoCommand = <<<BASH
cd /TinyTorch
export TITO_ROOT=/TinyTorch
tito module start {$moduleNumber} 2>&1
BASH;

        $tempScript = tempnam(sys_get_temp_dir(), 'tito_start_') . '.sh';
        file_put_contents($tempScript, $titoCommand);
        chmod($tempScript, 0755);
        
        $tinyTorchOutput = shell_exec("bash $tempScript 2>&1");
        unlink($tempScript);
        
        $checkResult = "<b style='color:purple'>Tito Module Start Command Executed!</b>";
    }
    
    // Run tito module complete command
    if ($action === 'tito_complete') {
        $moduleNumber = $_POST['module_number'] ?? '01';
        
        $titoCommand = <<<BASH
cd /TinyTorch
export TITO_ROOT=/TinyTorch
tito module complete {$moduleNumber} 2>&1
BASH;

        $tempScript = tempnam(sys_get_temp_dir(), 'tito_complete_') . '.sh';
        file_put_contents($tempScript, $titoCommand);
        chmod($tempScript, 0755);
        
        $tinyTorchOutput = shell_exec("bash $tempScript 2>&1");
        unlink($tempScript);
        
        $checkResult = "<b style='color:green'>Tito Module Complete Command Executed!</b>";
    }
    
    // Run tito system health
    if ($action === 'tito_health') {
        $titoCommand = <<<BASH
cd /TinyTorch
export TITO_ROOT=/TinyTorch
tito system health 2>&1
BASH;

        $tempScript = tempnam(sys_get_temp_dir(), 'tito_health_') . '.sh';
        file_put_contents($tempScript, $titoCommand);
        chmod($tempScript, 0755);
        
        $tinyTorchOutput = shell_exec("bash $tempScript 2>&1");
        unlink($tempScript);
        
        $checkResult = "<b style='color:teal'>Tito System Health Check Complete!</b>";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP + TinyTorch Environment v5</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        h3 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        h4 {
            color: #555;
            border-bottom: 2px solid #667eea;
            padding-bottom: 8px;
            margin-top: 30px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }
        form {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input[type="text"] {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            width: 60%;
            font-size: 16px;
        }
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        button, input[type="submit"] {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(17, 153, 142, 0.4);
        }
        .btn-info {
            background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
        }
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(58, 123, 213, 0.4);
        }
        .results-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            margin-top: 20px;
            word-wrap: break-word;
        }
        .results-box p {
            margin: 10px 0;
            padding: 8px;
            background: white;
            border-radius: 4px;
        }
        .code-output {
            background: #282c34;
            color: #abb2bf;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.6;
            overflow-x: auto;
            white-space: pre-wrap;
            margin-top: 15px;
        }
        .status-message {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
            background: #e7f3ff;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 8px;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>🚀 PHP + TinyTorch Environment</h3>
        <p class="subtitle">Interactive Machine Learning Demonstration</p>

        <form action="" method="post">
            <div class="form-group">
                <label for="myText01"><strong>Magic Word Check:</strong></label><br>
                <input type="text" id="myText01" name="myText01" value="" placeholder="Enter magic word...">
            </div>
            
            <div class="button-group">
                <button type="submit" name="action" value="check" class="btn-primary">Check Magic Word</button>
                <button type="submit" name="action" value="import_test" class="btn-success">Test TinyTorch Imports</button>
                <button type="submit" name="action" value="tensor_demo" class="btn-info">Run Tensor Demo</button>
            </div>
        </form>

        <h4 style="margin-top: 30px;">🔧 TinyTorch CLI Commands</h4>
        <form action="" method="post">
            <div class="form-group">
                <label for="module_number"><strong>Module Number:</strong></label><br>
                <input type="text" id="module_number" name="module_number" value="01" placeholder="01" style="width: 20%;">
            </div>
            
            <div class="button-group">
                <button type="submit" name="action" value="tito_start" class="btn-primary">
                    📝 Start Module
                </button>
                <button type="submit" name="action" value="tito_complete" class="btn-success">
                    ✅ Complete Module
                </button>
                <button type="submit" name="action" value="tito_health" class="btn-info">
                    🏥 System Health
                </button>
            </div>
        </form>
        
        <div class="status-message">
            <?php echo $checkResult; ?>
        </div>

        <h4>📊 Environment Information</h4>
        <div class="results-box">
            <p><strong>Node.js:</strong> <code><?php echo htmlspecialchars($nodeVersion); ?></code>
                <?php echo (strpos($nodeVersion, 'v') === 0) ? '<span class="badge badge-success">✓ Available</span>' : '<span class="badge badge-danger">✗ Not Found</span>'; ?>
            </p>
            <p><strong>Python:</strong> <code><?php echo htmlspecialchars($pythonVersion); ?></code>
                <?php echo (strpos($pythonVersion, 'Python') === 0) ? '<span class="badge badge-success">✓ Available</span>' : '<span class="badge badge-danger">✗ Not Found</span>'; ?>
            </p>
            <p><strong>TinyTorch:</strong> <code><?php echo htmlspecialchars($titoVersion); ?></code>
                <?php echo (strpos($titoVersion, 'TinyTorch found') !== false) ? '<span class="badge badge-success">✓ Installed</span>' : '<span class="badge badge-danger">✗ Not Found</span>'; ?>
            </p>
            <p style="font-size: 0.9em; color: #666; margin-top: 15px;">
                <strong>ℹ️ About TinyTorch:</strong> This is an educational framework where you implement modules yourself.<br>
                Use the buttons above to check installation status and run demos.
            </p>
        </div>

        <?php if (!empty($tinyTorchOutput)): ?>
        <h4>🔍 TinyTorch Import Test Results</h4>
        <div class="code-output"><?php echo htmlspecialchars($tinyTorchOutput); ?></div>
        <?php endif; ?>

        <?php if (!empty($tensorDemo)): ?>
        <h4>🎯 TinyTorch Tensor Demo Output</h4>
        <div class="code-output"><?php echo htmlspecialchars($tensorDemo); ?></div>
        <?php endif; ?>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 12px;">
            <p>PHP + TinyTorch Integration • Version 5.0</p>
        </div>
    </div>
</body>
</html>
