# my-examples-of-tinytorch
Just trying to get tinytorch working



[mlsysbook.ai](https://harvard-edge.github.io/cs249r_book/)

[TinyTorch](https://tinytorch.ai/quickstart-guide.html)


TinyTorch Github at https://github.com/mlsysbook/TinyTorch



This render.com link should be at 


  https://my-examples-of-tinytorch-2.onrender.com


  .



  .



  Supper useful command

```
tito src export --all

```

I have been having difficulties with the .ipyn notebooks not being generated

  Other githubs connected to this project


  [Github my-examples-of-tito](https://github.com/hpssjellis/my-examples-of-tito)   [Active Render Link](https://my-examples-of-tito.onrender.com/)


  

  [Github my-example-jupyter-tinyTorch](https://github.com/hpssjellis/my-example-jupyter-tinyTorch) [Active Render.com ](https://my-example-jupyter-tinytorch.onrender.com/)
  


  







# Installation

    ◦ pip install NumPy (Required for the core framework, as TinyTorch is built using only NumPy).
    ◦ pip install Jupyter (Required for interactive development notebooks).
    ◦ pip install Rich (Required for rich CLI visualizations).
    ◦ pip install PyTorch (Required specifically for validation, although the framework itself uses zero PyTorch dependencies).
    ◦ pip install PyYAML.
    ◦ pip install pytest (Required for the comprehensive validation system).
    ◦ pip install jupytext.



pyTorch not installing well.
```
pip3 install torch torchvision torchaudio --index-url https://download.pytorch.org/whl/cpu
```


# tito

# TinyTorch CLI (`tito`) Commands Reference

The `tito` Command Line Interface (CLI) provides essential tools for students, instructors, and TAs to manage the TinyTorch environment, track progress, export code, and handle grading workflows.

## Module Workflow and Development (Students)

These commands are used daily by students during the core three-step workflow (Edit → Export → Validate):

| Command | Purpose |
|---------|---------|
| `tito module complete MODULE_NUMBER` | Exports the completed module implementation (e.g., `tito module complete 01`) from the Jupyter notebook to the main TinyTorch package, making the code importable |
| `tito checkpoint status` | Checks the status of modules completed, used for optional self-assessment and progress tracking |

## System Diagnostics and Information

These commands verify the environment setup and provide system-level details:

| Command | Purpose |
|---------|---------|
| `tito system health` | Verifies the installation and runs system diagnostics (used during initial setup and instructor health checks) |
| `tito system doctor` | Runs system diagnostics to verify setup (should show all green checkmarks) |
| `tito system info` | Provides general system information |
| `tito module status --comprehensive` | Provides comprehensive system health monitoring and status information |

## Community and Benchmarking

These commands relate to optional features for community engagement and validation:

| Command | Purpose |
|---------|---------|
| `tito community join` | Joins the global TinyTorch community (joining is optional and may include sharing information like country or institution) |
| `tito benchmark baseline` | Runs a baseline benchmark to validate the setup and confirm the installation is working ("Hello World" moment) |

## Grading and Course Management (Instructors/TAs)

These commands wrap the NBGrader functionality for assignment distribution, collection, and scoring:

| Command | Purpose |
|---------|---------|
| `tito grade setup` | Initializes the grading environment and NBGrader integration (used during instructor setup) |
| `tito grade generate [module]` | Generates the instructor version of an assignment, which includes solutions (e.g., `tito grade generate 01_tensor`) |
| `tito grade release [module]` | Creates the student version of an assignment by removing solutions, ready for distribution (e.g., `tito grade release 01_tensor`) |
| `tito grade collect [module]` | Collects all student submissions for a specified module (e.g., `tito grade collect 01_tensor`) |
| `tito grade collect [module] --student [id]` | Collects the submission for a specific student ID |
| `tito grade autograde [module]` | Runs automated grading for all submissions of a specified module (e.g., `tito grade autograde 01_tensor`) |
| `tito grade autograde [module] --student [id]` | Runs automated grading for a specific student's submission |
| `tito grade manual [module]` | Opens the browser-based interface for manual review and grading |
| `tito grade export` | Exports all grades to a CSV file |
| `tito grade export --module [module] --output [file.csv]` | Exports grades for a specific module to a specified file |
| `tito checkpoint status --student ID` | Tracks the progress and checkpoint achievements for a specific student |

---

## Quick Reference: Common Workflows

### Student Workflow
1. Edit code in Jupyter notebook
2. `tito module complete 01` (export to package)
3. Validate implementation
4. `tito checkpoint status` (check progress)

### Instructor Workflow
1. `tito grade setup` (one-time setup)
2. `tito grade generate 01_tensor` (create assignment with solutions)
3. `tito grade release 01_tensor` (create student version)
4. `tito grade collect 01_tensor` (gather submissions)
5. `tito grade autograde 01_tensor` (automated grading)
6. `tito grade manual 01_tensor` (manual review if needed)
7. `tito grade export` (export final grades)
