DROP DATABASE IF EXISTS BookStore;
CREATE DATABASE BookStore;

use BookStore;

CREATE TABLE Categories (
	CategoryID int NOT NULL DEFAULT 0 ,
	CategoryDescription varchar (75) NULL ,
	PRIMARY KEY (CategoryID),
	INDEX (CategoryID)
);

CREATE TABLE PaymentOptions (
	PaymentOptionID int NOT NULL DEFAULT 0 ,
	PaymentOptionName varchar (50) NULL ,
	PRIMARY KEY (PaymentOptionID),
	INDEX (PaymentOptionID)
);

CREATE TABLE Customers (
	CustomerID int NOT NULL AUTO_INCREMENT,
	CustFirstName varchar (25) NULL ,
	CustLastName varchar (25) NULL ,
	PRIMARY KEY (CustomerID),
	INDEX (CustomerID)
);

CREATE TABLE BookInventories (
	BookID int NOT NULL AUTO_INCREMENT,
	BookName varchar (50) NULL ,
  BookImgUrl varchar (255) NULL ,
	BookAuthor varchar (100) NULL ,
	RetailPrice decimal (15, 2) NULL DEFAULT 0 ,
	Quantity smallint NULL DEFAULT 0 ,
	CategoryID int NULL DEFAULT 0,
	PRIMARY KEY (BookID),
	INDEX (BookID)
);

CREATE TABLE BookInventoryOrders (
	OrderID int NOT NULL AUTO_INCREMENT,
  CustomerID int NOT NULL ,
	OrderDate date NULL ,
  PaymentOptionID int NOT NULL DEFAULT 0, 
  CardNumber varchar (16) NULL ,
  CardExpiresIn varchar (7) NULL ,
	PRIMARY KEY (OrderID),
	INDEX (OrderID)
);

CREATE TABLE BookInventoryOrder_Details (
	OrderID int NOT NULL DEFAULT 0 ,
	BookID int NOT NULL DEFAULT 0 ,
	QuotedPrice decimal (15, 2) NULL DEFAULT 0 ,
	QuantityOrdered smallint NULL DEFAULT 0 
);

ALTER TABLE BookInventoryOrder_Details ADD 
	CONSTRAINT BookInventoryOrder_Details_PK PRIMARY KEY   
	(
		OrderID,
		BookID
	);
 CREATE  INDEX OrderOrderDetails ON BookInventoryOrder_Details(OrderID);
 CREATE  INDEX BookOrderDetails ON BookInventoryOrder_Details(BookID);

ALTER TABLE BookInventoryOrder_Details  
	ADD CONSTRAINT BookInventoryOrder_Details_FK00 FOREIGN KEY 
	(
		OrderID
	) REFERENCES BookInventoryOrders (
		OrderID
	),
	ADD CONSTRAINT BookInventoryOrder_Details_FK01 FOREIGN KEY 
	(
		BookID
	) REFERENCES BookInventories (
		BookID
	);

ALTER TABLE BookInventoryOrders 
  ADD CONSTRAINT BookInventoryOrders_FK00 FOREIGN KEY 
	(
		CustomerID
	) REFERENCES Customers (
		CustomerID
	),
	ADD CONSTRAINT BookInventoryOrders_FK01 FOREIGN KEY 
	(
		CustomerID
	) REFERENCES Customers (
		CustomerID
	),
	ADD CONSTRAINT BookInventoryOrders_FK02 FOREIGN KEY 
	(
		PaymentOptionID
	) REFERENCES PaymentOptions (
		PaymentOptionID
	);

ALTER TABLE BookInventories ADD 
	CONSTRAINT BookInventories_FK00 FOREIGN KEY 
	(
		CategoryID
	) REFERENCES Categories (
		CategoryID
	);


