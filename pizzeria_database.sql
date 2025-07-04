USE [master]
GO
/****** Object:  Database [pizzeria]    Script Date: 26/06/2025 17:25:44 ******/
CREATE DATABASE [pizzeria]
 CONTAINMENT = NONE
 ON  PRIMARY 
( NAME = N'pizzeria', FILENAME = N'/var/opt/mssql/data/pizzeria.mdf' , SIZE = 8192KB , MAXSIZE = UNLIMITED, FILEGROWTH = 65536KB )
 LOG ON 
( NAME = N'pizzeria_log', FILENAME = N'/var/opt/mssql/data/pizzeria_log.ldf' , SIZE = 8192KB , MAXSIZE = 2048GB , FILEGROWTH = 65536KB )
 WITH CATALOG_COLLATION = DATABASE_DEFAULT
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [pizzeria].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [pizzeria] SET ANSI_NULL_DEFAULT OFF 
GO
ALTER DATABASE [pizzeria] SET ANSI_NULLS OFF 
GO
ALTER DATABASE [pizzeria] SET ANSI_PADDING OFF 
GO
ALTER DATABASE [pizzeria] SET ANSI_WARNINGS OFF 
GO
ALTER DATABASE [pizzeria] SET ARITHABORT OFF 
GO
ALTER DATABASE [pizzeria] SET AUTO_CLOSE OFF 
GO
ALTER DATABASE [pizzeria] SET AUTO_SHRINK OFF 
GO
ALTER DATABASE [pizzeria] SET AUTO_UPDATE_STATISTICS ON 
GO
ALTER DATABASE [pizzeria] SET CURSOR_CLOSE_ON_COMMIT OFF 
GO
ALTER DATABASE [pizzeria] SET CURSOR_DEFAULT  GLOBAL 
GO
ALTER DATABASE [pizzeria] SET CONCAT_NULL_YIELDS_NULL OFF 
GO
ALTER DATABASE [pizzeria] SET NUMERIC_ROUNDABORT OFF 
GO
ALTER DATABASE [pizzeria] SET QUOTED_IDENTIFIER OFF 
GO
ALTER DATABASE [pizzeria] SET RECURSIVE_TRIGGERS OFF 
GO
ALTER DATABASE [pizzeria] SET  ENABLE_BROKER 
GO
ALTER DATABASE [pizzeria] SET AUTO_UPDATE_STATISTICS_ASYNC OFF 
GO
ALTER DATABASE [pizzeria] SET DATE_CORRELATION_OPTIMIZATION OFF 
GO
ALTER DATABASE [pizzeria] SET TRUSTWORTHY OFF 
GO
ALTER DATABASE [pizzeria] SET ALLOW_SNAPSHOT_ISOLATION OFF 
GO
ALTER DATABASE [pizzeria] SET PARAMETERIZATION SIMPLE 
GO
ALTER DATABASE [pizzeria] SET READ_COMMITTED_SNAPSHOT OFF 
GO
ALTER DATABASE [pizzeria] SET HONOR_BROKER_PRIORITY OFF 
GO
ALTER DATABASE [pizzeria] SET RECOVERY FULL 
GO
ALTER DATABASE [pizzeria] SET  MULTI_USER 
GO
ALTER DATABASE [pizzeria] SET PAGE_VERIFY CHECKSUM  
GO
ALTER DATABASE [pizzeria] SET DB_CHAINING OFF 
GO
ALTER DATABASE [pizzeria] SET FILESTREAM( NON_TRANSACTED_ACCESS = OFF ) 
GO
ALTER DATABASE [pizzeria] SET TARGET_RECOVERY_TIME = 60 SECONDS 
GO
ALTER DATABASE [pizzeria] SET DELAYED_DURABILITY = DISABLED 
GO
ALTER DATABASE [pizzeria] SET ACCELERATED_DATABASE_RECOVERY = OFF  
GO
EXEC sys.sp_db_vardecimal_storage_format N'pizzeria', N'ON'
GO
ALTER DATABASE [pizzeria] SET QUERY_STORE = ON
GO
ALTER DATABASE [pizzeria] SET QUERY_STORE (OPERATION_MODE = READ_WRITE, CLEANUP_POLICY = (STALE_QUERY_THRESHOLD_DAYS = 30), DATA_FLUSH_INTERVAL_SECONDS = 900, INTERVAL_LENGTH_MINUTES = 60, MAX_STORAGE_SIZE_MB = 1000, QUERY_CAPTURE_MODE = AUTO, SIZE_BASED_CLEANUP_MODE = AUTO, MAX_PLANS_PER_QUERY = 200, WAIT_STATS_CAPTURE_MODE = ON)
GO
USE [pizzeria]
GO
/****** Object:  Table [dbo].[Ingredient]    Script Date: 26/06/2025 17:25:44 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Ingredient](
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Pizza_Order]    Script Date: 26/06/2025 17:25:44 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Pizza_Order](
	[order_id] [int] IDENTITY(1,1) NOT NULL,
	[client_username] [nvarchar](255) NULL,
	[client_name] [nvarchar](255) NOT NULL,
	[personnel_username] [nvarchar](255) NOT NULL,
	[datetime] [datetime] NOT NULL,
	[status] [int] NULL,
	[address] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[order_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Pizza_Order_Product]    Script Date: 26/06/2025 17:25:44 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Pizza_Order_Product](
	[order_id] [int] NOT NULL,
	[product_name] [nvarchar](255) NOT NULL,
	[quantity] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[order_id] ASC,
	[product_name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Product]    Script Date: 26/06/2025 17:25:44 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Product](
	[name] [nvarchar](255) NOT NULL,
	[price] [decimal](10, 2) NOT NULL,
	[type_id] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Product_Ingredient]    Script Date: 26/06/2025 17:25:44 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Product_Ingredient](
	[product_name] [nvarchar](255) NOT NULL,
	[ingredient_name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[product_name] ASC,
	[ingredient_name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ProductType]    Script Date: 26/06/2025 17:25:44 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ProductType](
	[name] [nvarchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[User]    Script Date: 26/06/2025 17:25:44 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[User](
	[username] [nvarchar](255) NOT NULL,
	[password] [nvarchar](255) NOT NULL,
	[first_name] [nvarchar](255) NOT NULL,
	[last_name] [nvarchar](255) NOT NULL,
	[address] [nvarchar](255) NULL,
	[role] [nvarchar](50) NOT NULL,
	[is_admin] [bit] NULL,
PRIMARY KEY CLUSTERED 
(
	[username] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
ALTER TABLE [dbo].[User] ADD  DEFAULT ((0)) FOR [is_admin]
GO
ALTER TABLE [dbo].[Pizza_Order]  WITH CHECK ADD FOREIGN KEY([client_username])
REFERENCES [dbo].[User] ([username])
GO
ALTER TABLE [dbo].[Pizza_Order]  WITH CHECK ADD FOREIGN KEY([personnel_username])
REFERENCES [dbo].[User] ([username])
GO
ALTER TABLE [dbo].[Pizza_Order_Product]  WITH CHECK ADD FOREIGN KEY([order_id])
REFERENCES [dbo].[Pizza_Order] ([order_id])
GO
ALTER TABLE [dbo].[Pizza_Order_Product]  WITH CHECK ADD FOREIGN KEY([product_name])
REFERENCES [dbo].[Product] ([name])
GO
ALTER TABLE [dbo].[Product]  WITH CHECK ADD FOREIGN KEY([type_id])
REFERENCES [dbo].[ProductType] ([name])
GO
ALTER TABLE [dbo].[Product_Ingredient]  WITH CHECK ADD FOREIGN KEY([ingredient_name])
REFERENCES [dbo].[Ingredient] ([name])
GO
ALTER TABLE [dbo].[Product_Ingredient]  WITH CHECK ADD FOREIGN KEY([product_name])
REFERENCES [dbo].[Product] ([name])
GO
USE [master]
GO
ALTER DATABASE [pizzeria] SET  READ_WRITE 
GO
