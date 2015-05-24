# Beat
How to deploy this application:

1. Copy the whole project to apache's www root folder
2. To Run the test for part 1 and part 2, just type yourdomain/test.php and you will see the input fields of the tests. The requests are called via Ajax.
3. Before running part 3, configure the /db/DB.php file to specify the server's database connection detail in DB class at first. Then run the DB script file install_files/db.sql to create the database tables. At last, you can try to test the api the instruction specified.
 

NOTE: I didn't do too much on user's input validation. I hope the application will suit your requirements.
