# HighFive-Framework [Work In Progress...]
A new modular (NO MVC!) library for any existing and new project, created from scratch or with other scripts/frameworks!
```php
$HF->class->function();
$HF->class->classVar;
```
You can create any class you need, just name the file as you want the class name will be accessible and prefix it with "HF".

Pratical example. We want to create a car class:
```
1. Create a file named "car.HF.php" into the "/lib" folder
2. Create a class: "class HFcar{ ... }"
```
Now You can access to it directly or via $HF main object:
```
$HF->car->varName;
$HF->car->function();
HFcar::varName;
HFcar::function();
```
That's it.

Cool, uh?
