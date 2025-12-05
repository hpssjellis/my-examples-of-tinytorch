import numpy as np

class Tensor:
    """
    The core Tensor class.
    """
    
    # FIX 1: Use 'data' instead of 'myData' to match app.py test logic
    # FIX 2: Use 'shape' instead of 'myShape'
    def __init__(self, myArrayData):
        """
        Initializes the Tensor, storing data as a NumPy array.
        """
        self.data = np.array(myArrayData, dtype=np.float32)
        self.shape = self.data.shape

    # ----------------------------------------------------
    # String Representation (No change needed)
    # ----------------------------------------------------
    
    def __repr__(self):
        """
        Returns a clean string representation of the Tensor for printing.
        """
        return f"Tensor(data={self.data})"

    # ----------------------------------------------------
    # Addition Operation (No change needed)
    # ----------------------------------------------------

    def __add__(self, myOtherTensor):
        """
        Defines the behavior for the '+' operator (self + other).
        """
        # We rely on NumPy's efficient element-wise addition
        myNewData = self.data + myOtherTensor.data
        
        # The result must be wrapped back in a new Tensor object
        # The new Tensor will correctly use the 'data' property
        myResultTensor = Tensor(myNewData)
        return myResultTensor
