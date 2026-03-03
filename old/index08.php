<?php
// PHP INCLUDES
// The 'require_once' is used to load your tinytorch library, as in the original file.
require_once 'tinytorch/tinytorch.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>TinyTorch Examples (Rewritten Index)</title>
    </head>
<body style="font-family: Arial, sans-serif;">

    <h1 style="text-align:center;">TinyTorch Examples and Editor</h1>

    <div id="myMainContainer" style="display:flex; flex-wrap: wrap; gap: 20px;">

        <div style="flex: 1 1 450px; border: 1px solid #ccc; padding: 10px; min-width: 300px;">
            <h2>GitHub Repository Embed</h2>
            <p>The <code style="background-color:#eee; padding: 2px 4px;">&lt;iframe&gt;</code> tag is the simple way to display an external website.</p>
            <iframe
                id="myGitHubFrame"
                src="https://github.com/hpssjellis/my-examples-of-tinytorch"
                style="width: 100%; height: 400px; border: 1px solid blue;"
            ></iframe>
        </div>

        <div style="flex: 1 1 450px; border: 1px solid #ccc; padding: 10px; min-width: 300px;">
            <h2>Example List (Original Functionality)</h2>
            <div id="myExampleList"></div>
            <a onclick="myTinyTorchRun()" style="padding: 5px; background-color: #d9e7f5; border: 1px solid #ccc; text-decoration: none; cursor:pointer;">Reload Examples</a>
        </div>

    </div>

    <hr style="margin: 30px 0; border-top: 2px solid #aaa;">

    <div style="border: 2px solid #c00; padding: 15px; margin-top: 20px;">
        <h2>Included editor.php Code (Using PHP 'include')</h2>
        <p>The PHP <code style="background-color:#fee; padding: 2px 4px;">include 'editor.php';</code> statement executes and inserts the content of the local file here.</p>
        <?php
            // The 'include' function is the simplest way to load and run the code
            // (HTML, CSS, PHP, JS) from a local PHP file within the current file.
            include 'editor.php';
        ?>
    </div>


    <script>
        // Use descriptive camelCase and 'my' prefix for variables and functions.
        const myExampleListDiv = document.getElementById('myExampleList');

        /**
         * Fetches a list of JSON files from the server and generates links to editor.php.
         * Uses async/await promise format.
         */
        async function myTinyTorchRun() {
            try {
                // Assuming this endpoint (file-list.php) exists to generate the list of examples
                const myResponse = await fetch('tinytorch/file-list.php');
                if (!myResponse.ok) {
                    throw new Error(`HTTP error! status: ${myResponse.status}`);
                }
                const myFileNames = await myResponse.json();

                let myHtmlContent = '<ul>';
                // Generate a simple static link for each example file
                myFileNames.forEach(myFileName => {
                    myHtmlContent += `<li><a href="editor.php?file=${myFileName}" style="text-decoration: none; color: darkgreen;">${myFileName}</a></li>`;
                });
                myHtmlContent += '</ul>';

                myExampleListDiv.innerHTML = myHtmlContent;

            } catch (myError) {
                console.error('Error fetching example list:', myError);
                myExampleListDiv.innerHTML = `<p style="color: red;">Could not load examples. Check server configuration.</p>`;
            }
        }

        // Load examples when the page loads
        myTinyTorchRun();

    </script>
</body>
</html>
