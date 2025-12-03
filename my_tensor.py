import numpy as np

class Tensor:
    """
    A core class representing a multi-dimensional array of numbers.
    This is the building block of our neural network framework.
    It primarily wraps a NumPy array for fast operations.
    """
    
    # Following user preference: Descriptive camelCase names
    def __init__(self, myArrayData):
        """
        Initializes the Tensor.
        
        Args:
            myArrayData: The data to store, which can be a list, tuple, or NumPy array.
        """
        # Convert all input data to a NumPy array for consistent handling and performance
        self.myData = np.array(myArrayData, dtype=np.float32)
        
        # Store the shape of the tensor (inherited from the NumPy array)
        self.myShape = self.myData.shape

    # ----------------------------------------------------
    # Assignment Requirement 1: String Representation
    # ----------------------------------------------------
    
    def __repr__(self):
        """
        Returns a clean string representation of the Tensor for printing.
        """
        return f"Tensor(data={self.myData})"

    # ----------------------------------------------------
    # Assignment Requirement 2: Addition Operation
    # ----------------------------------------------------

    def __add__(self, myOtherTensor):
        """
        Defines the behavior for the '+' operator (self + other).
        Performs element-wise addition between two Tensors.
        """
        # We rely on NumPy's efficient element-wise addition
        myNewData = self.myData + myOtherTensor.myData
        
        # The result must be wrapped back in a new Tensor object
        myResultTensor = Tensor(myNewData)
        return myResultTensor
        
    # Example of how a student would use this:
    # t1 = Tensor([[1.0, 2.0]])
    # t2 = Tensor([[3.0, 4.0]])
    # t3 = t1 + t2
    # print(t3) # Should print: Tensor(data=[[4. 6.]])