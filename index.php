<?php
// Load the bridge library
// Note: Ensure tinytorch.php exists in your folder!
if(file_exists('tinytorch/tinytorch.php')){
    require_once 'tinytorch/tinytorch.php';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>TinyTorch Examples</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">

    <h1 style="text-align:center;">TinyTorch Examples and Editor</h1>

    <div id="myMainContainer" style="display:flex; flex-wrap: wrap; gap: 20px; justify-content: center;">

        <div style="flex: 1 1 450px; border: 1px solid #ccc; padding: 15px; background: white; border-radius: 8px;">
            <h2>GitHub Repository</h2>
            <iframe
                id="myGitHubFrame"
                src="https://github.com/hpssjellis/my-examples-of-tinytorch"
                style="width: 100%; height: 400px; border: 1px solid #007bff; border-radius: 4px;"
            ></iframe>
        </div>

        <div style="flex: 1 1 450px; border: 1px solid #ccc; padding: 15px; background: white; border-radius: 8px;">
            <h2>Example List</h2>
            <div id="myExampleList" style="min-height: 100px; margin-bottom: 15px;">
                <p>Loading files...</p>
            </div>
            <a href="javascript:void(0)" onclick="myTinyTorchRun()" style="padding: 10px 20px; background-color: #007bff; color: white; border-radius: 5px; text-decoration: none; display: inline-block;">Reload Examples</a>
        </div>

    </div>

    <hr style="margin: 40px 0; border: 0; border-top: 2px solid #ddd;">

    <div style="border: 2px solid #c00; padding: 20px; background: #fff5f5; border-radius: 8px;">
        <h2>Editor</h2>
        <?php
            if(file_exists('editor.php')){
                include 'editor.php';
            } else {
                echo "<p style='color:red;'>editor.php not found.</p>";
            }
        ?>
    </div>

    <script>
        const myExampleListDiv = document.getElementById('myExampleList');

        async function myTinyTorchRun() {
            myExampleListDiv.innerHTML = "Refreshing...";
            try {
                // Pointing to the file created in step 2
                const myResponse = await fetch('tinytorch/file-list.php');
                if (!myResponse.ok) throw new Error(`HTTP Error: ${myResponse.status}`);
                
                const myFileNames = await myResponse.json();

                if(myFileNames.length === 0) {
                    myExampleListDiv.innerHTML = "No files found.";
                    return;
                }

                let myHtmlContent = '<ul style="line-height: 2;">';
                myFileNames.forEach(myFileName => {
                    // Static links to the editor
                    myHtmlContent += `<li><a href="editor.php?file=${encodeURIComponent(myFileName)}" style="color: #2c3e50; font-weight: bold;">${myFileName}</a></li>`;
                });
                myHtmlContent += '</ul>';

                myExampleListDiv.innerHTML = myHtmlContent;

            } catch (myError) {
                console.error('Error:', myError);
                myExampleListDiv.innerHTML = `<p style="color: red;">Error connecting to backend.</p>`;
            }
        }

        myTinyTorchRun();
    </script>
</body>
</html>
