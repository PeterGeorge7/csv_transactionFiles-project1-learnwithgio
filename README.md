### Tasks:
#### 1- read all csv files
#### 2- store in array
#### 3- calc income & expense & net total
#### 4- date format

### explaining How Final Code Works..
#### 1- first of all the logic gone in App.php 
#### 2- getTransactions takes the result of getAllFilesNames that return all files in the files folder to get all transactions line in all files
#### 3- then inside the getTransactions it checks if there is a callable handler can be diffrence if there is diffrent formats in files and save the formated with key => value array to be easily access 
#### 4- then in index.php in the public we require all of logic and view then make the connection between them in code
