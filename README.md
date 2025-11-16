## Solutions
 1. Unidirectional database relations
 2. Binary bitmask

Chosen solution 1. Unidirectional database relations. 
Chose first solution over binary bitmask for simplicity, maintability. 
While 2. would be more memory and performance efficient, codebase and solution would be more complex.


## Test Data
Color = Blue   -> Size: Any (Small, Medium, Large)

Size = Small   -> Material: Cotton only  
Size = Medium  -> Material: Any (Cotton, Polyester)
Size = Large   -> Material: Polyester only

#### VALID COMBINATIONS:
Red + Small + Cotton
Red + Medium + Cotton  
Red + Medium + Polyester
Blue + Small + Cotton
Blue + Medium + Cotton
Blue + Medium + Polyester
Blue + Large + Polyester

#### INVALID COMBINATIONS:
Red + Large (any material)
Any Color + Small + Polyester
Any Color + Large + Cotton
