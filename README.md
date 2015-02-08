# HighFive-Framework [Work In Progress...]
A new modular (NO MVC!) library for any existing and new project, created from scratch or with other scripts/frameworks!
```php
$HF->class->function();
$HF->class->classVar;
```
You can create any class you need, just name the file as you want the class name will be accessible and prefix it with "HF".

Pratical example. We want to create a "animal" class:
```
1. Create a file named "animal.HF.php" into the "/lib" folder
2. Create a class: "class HFanimal{ ... }"
```
Now You can access to it directly or via $HF main object:
```php
$HF->animal->varName;
$HF->animal->function();
HFanimal::varName;
HFanimal::function();

//EXAMPLES
$HF->animal->eyes("blue"); //setting the eyes of generic animal to blue
$HF->animal->color("brown")->type("dog"); //return a brown dog with blue eyes (because of the line above)
$HF::animal::takeTheBone(); //send the brown dog with blue eyes to take the bone!
```
That's it. Really limitless.

Cool, uh?
