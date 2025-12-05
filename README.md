# my-examples-of-tinytorch
Just trying to get tinytorch working



[mlsysbook.ai](https://harvard-edge.github.io/cs249r_book/)

[TinyTorch](https://tinytorch.ai/quickstart-guide.html)



This render.com link should be at https://my-examples-of-tinytorch-1.onrender.com/   that one was bad


This might work   https://my-examples-of-tinytorch-2.onrender.com







# Installation

    ◦ pip install NumPy (Required for the core framework, as TinyTorch is built using only NumPy).
    ◦ pip install Jupyter (Required for interactive development notebooks).
    ◦ pip install Rich (Required for rich CLI visualizations).
    ◦ pip install PyTorch (Required specifically for validation, although the framework itself uses zero PyTorch dependencies).
    ◦ pip install PyYAML.
    ◦ pip install pytest (Required for the comprehensive validation system).
    ◦ pip install jupytext.





# tito

TinyTorch CLI (tito) Commands Reference
The tito Command Line Interface (CLI) provides essential tools for students, instructors, and TAs to manage the TinyTorch environment, track progress, export code, and handle grading workflows.
Module Workflow and Development (Students)
These commands are used daily by students during the core three-step workflow (Edit → Export → Validate):
CommandPurposetito module complete MODULE_NUMBERExports the completed module implementation (e.g., tito module complete 01) from the Jupyter notebook to the main TinyTorch package, making the code importabletito checkpoint statusChecks the status of modules completed, used for optional self-assessment and progress tracking
System Diagnostics and Information
These commands verify the environment setup and provide system-level details:
CommandPurposetito system healthVerifies the installation and runs system diagnostics (used during initial setup and instructor health checks)tito system doctorRuns system diagnostics to verify setup (should show all green checkmarks)tito system infoProvides general system informationtito module status --comprehensiveProvides comprehensive system health monitoring and status information
Community and Benchmarking
These commands relate to optional features for community engagement and validation:
CommandPurposetito community joinJoins the global TinyTorch community (joining is optional and may include sharing information like country or institution)tito benchmark baselineRuns a baseline benchmark to validate the setup and confirm the installation is working ("Hello World" moment)
Grading and Course Management (Instructors/TAs)
These commands wrap the NBGrader functionality for assignment distribution, collection, and scoring:
CommandPurposetito grade setupInitializes the grading environment and NBGrader integration (used during instructor setup)tito grade generate [module]Generates the instructor version of an assignment, which includes solutions (e.g., tito grade generate 01_tensor)tito grade release [module]Creates the student version of an assignment by removing solutions, ready for distribution (e.g., tito grade release 01_tensor)tito grade collect [module]Collects all student submissions for a specified module (e.g., tito grade collect 01_tensor)tito grade collect [module] --student [id]Collects the submission for a specific student IDtito grade autograde [module]Runs automated grading for all submissions of a specified module (e.g., tito grade autograde 01_tensor)tito grade autograde [module] --student [id]Runs automated grading for a specific student's submissiontito grade manual [module]Opens the browser-based interface for manual review and gradingtito grade exportExports all grades to a CSV filetito grade export --module [module] --output [file.csv]Exports grades for a specific module to a specified filetito checkpoint status --student IDTracks the progress and checkpoint achievements for a specific student

Quick Reference: Common Workflows
Student Workflow

Edit code in Jupyter notebook
tito module complete 01 (export to package)
Validate implementation
tito checkpoint status (check progress)

Instructor Workflow

tito grade setup (one-time setup)
tito grade generate 01_tensor (create assignment with solutions)
tito grade release 01_tensor (create student version)
tito grade collect 01_tensor (gather submissions)
tito grade autograde 01_tensor (automated grading)
tito grade manual 01_tensor (manual review if needed)
tito grade export (export final grades)
    
