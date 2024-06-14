DELETE FROM ReservedBooks WHERE DATEDIFF( NOW( ) ,  timestamp ) >=2;
