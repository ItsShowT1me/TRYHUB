-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2025 at 01:34 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `login_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `mbti` varchar(4) DEFAULT NULL,
  `about` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `banned_until` datetime DEFAULT NULL,
  `interested_category` enum('game','music','movie','sport','tourism','other') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `user_name`, `password`, `date`, `email`, `phone`, `mbti`, `about`, `image`, `banned_until`, `interested_category`) VALUES
(5, 1001, 'AnanyaR', 'pass1234', '2025-08-12 21:30:59', 'ananya.r@gmail.com', '0891234567', 'INFJ', 'A passionate UI/UX designer from Bangkok.', 'uploads/profile_1001_1754240818.png', NULL, 'music'),
(6, 1002, 'ThanapatMC', '1234abcd', '2025-08-12 23:19:17', 'thanapat.mc@hotmail.com', '0819876543', 'ENTP', 'A tech lover who enjoys building things from scratch.', 'uploads/profile_1002_1754240852.png', NULL, 'music'),
(7, 1003, 'JiraKit', 'myp@ssword', '2025-08-03 11:33:34', 'jirapat.k@gmail.com', '0623456789', 'INFP', 'Quiet thinker who enjoys meaningful connections.', NULL, NULL, NULL),
(8, 1004, 'KanyaSuda', 'hello@123', '2025-08-03 11:33:34', 'k.srisuda@yahoo.com', '0837654321', 'ESFJ', 'Friendly, warm-hearted, and driven to help others.', NULL, NULL, NULL),
(9, 1005, 'NattaBM', 'boonmee99', '2025-08-03 11:33:34', 'nattawut.b@hotmail.com', '0951237890', 'ISTP', 'Love exploring how things work and solving problems.', NULL, NULL, NULL),
(10, 1006, 'PNamS', 'coolpass12', '2025-08-03 11:33:34', 'p.namsai@gmail.com', '0889911223', 'ENTJ', 'Strategic leader with a passion for development.', NULL, NULL, NULL),
(11, 1007, 'RattanaTW', 'rat@secure', '2025-08-03 11:33:34', 'rattana.tw@gmail.com', '0865544332', 'ISFJ', 'Loyal and considerate, always ready to help.', NULL, NULL, NULL),
(12, 1008, 'ChaninWW', 'wwchanin88', '2025-08-03 11:33:34', 'chanin.w@gmail.com', '0894455661', 'ENFP', 'Energetic, creative, and loves to inspire others.', NULL, NULL, NULL),
(13, 1009, 'SirinLS', 'sirinpass', '2025-08-03 11:33:34', 'sirin.ls@gmail.com', '0841122334', 'ISTJ', 'Responsible and values tradition and order.', NULL, NULL, NULL),
(14, 1010, 'PongThav', 'ptsecure', '2025-08-03 11:33:34', 'pongsak.t@hotmail.com', '0905566778', 'ESTP', 'Bold and spontaneous, lives life to the fullest.', NULL, NULL, NULL),
(15, 3001, 'jamie33', ')r8u^Zwm(!', '2025-08-03 16:16:01', 'obeck@hotmail.com', '0475530541', 'INFP', 'Dark experience drive art safe somebody.', NULL, NULL, NULL),
(16, 3002, 'cookmary', '+UE@&r5j6S', '2025-08-03 16:16:01', 'qrodriguez@bell-garner.com', '3250745204', 'ESFP', 'Our ten instead common create police clear memory for.', NULL, NULL, NULL),
(17, 3003, 'kimberlythomas', 'F+iHbDfe*3', '2025-08-03 16:16:01', 'maria00@yahoo.com', '985.599.39', 'ISTJ', 'Point song would need miss notice draw certain.', NULL, NULL, NULL),
(18, 3004, 'daniel17', 'SM+g*6Fr$v', '2025-08-03 16:16:01', 'morganjeremiah@gmail.com', '757.330.61', 'INFJ', 'Wear instead price common common western be ball certain good score yeah.', NULL, NULL, NULL),
(19, 3005, 'margaret98', '$Y%o^uhr2F', '2025-08-03 16:16:01', 'xhenderson@hotmail.com', '8170211905', 'ESFJ', 'Billion agree however building help son hand another.', NULL, NULL, NULL),
(20, 3006, 'dennis67', 'A2m%6lUt3(', '2025-08-03 16:16:01', 'tina08@campos.com', '7778575647', 'INTJ', 'Admit begin message need standard build.', NULL, NULL, NULL),
(21, 3007, 'stacy15', '!9)F)OFb&M', '2025-08-03 16:16:01', 'reneemcdonald@jensen.com', '1748045637', 'ESFJ', 'Tv wrong future here message least tax.', NULL, NULL, NULL),
(22, 3008, 'jeff31', '@2QB$P11nn', '2025-08-03 16:16:01', 'lauren09@hotmail.com', '2545592507', 'ENFP', 'Key next brother style institution world state more billion yes teach run.', NULL, NULL, NULL),
(23, 3009, 'jimenezkelly', '&A6XShexmg', '2025-08-03 16:16:01', 'nelsonjustin@glover.com', '+101942754', 'ENTP', 'Popular allow rate right mouth trouble owner behind marriage we cultural teacher seek.', NULL, NULL, NULL),
(24, 3010, 'irobinson', 'j5aRNA(D$z', '2025-08-03 16:16:01', 'colinperry@powers.biz', '3544508669', 'ESFJ', 'Outside into free one finally member rest learn executive article.', NULL, NULL, NULL),
(25, 3011, 'tayloranthony', '3#N7moUf$2', '2025-08-03 16:16:01', 'terricastaneda@yahoo.com', '0016961777', 'ESTJ', 'Hit shoulder compare drug maintain politics middle box drop girl.', NULL, NULL, NULL),
(26, 3012, 'cobbpatrick', 'W3&OSa_1(R', '2025-08-03 16:16:01', 'qnunez@dean.com', '0018688948', 'ESFJ', 'Glass couple thousand hair focus voice American them wife simple able strong catch.', NULL, NULL, NULL),
(27, 3013, 'jenniferbright', 's5JH+NuL&!', '2025-08-03 16:16:01', 'diana18@white-kim.com', '0328090667', 'INTP', 'Soon keep hour water success which baby state how loss.', NULL, NULL, NULL),
(28, 3014, 'ayerstimothy', '51jfp%Tm*b', '2025-08-03 16:16:01', 'carol59@hotmail.com', '0395435279', 'ISTJ', 'Story arrive time center majority region road performance stay.', NULL, NULL, NULL),
(29, 3015, 'bennettchristopher', '&BE2tKxF22', '2025-08-03 16:16:01', 'ingramjames@gay.com', '8736266596', 'ISFP', 'Green general campaign smile energy kid believe lot able these whole decade.', NULL, NULL, NULL),
(30, 3016, 'kaitlynsmith', '$1WCl6wGXO', '2025-08-03 16:16:01', 'james57@gmail.com', '4423153985', 'ESTJ', 'Eat professor number southern a training behavior hot particularly TV conference receive.', NULL, NULL, NULL),
(31, 3017, 'tonyramos', ')&v7EG!p&b', '2025-08-03 16:16:01', 'alvarezheather@guzman.biz', '+187604971', 'ENFJ', 'Decision reveal expert free pick cut.', NULL, NULL, NULL),
(32, 3018, 'qwhite', '1h+vAjjS(8', '2025-08-03 16:16:01', 'rjohnson@griffith.com', '0018735723', 'ESFP', 'Same Mrs discussion you wind plant material who adult us to significant.', NULL, NULL, NULL),
(33, 3019, 'dale73', 'X!&5pZ)v&r', '2025-08-03 16:16:01', 'carolyn20@gmail.com', '3103920985', 'INTJ', 'Information or its face agree growth.', NULL, NULL, NULL),
(34, 3020, 'chayes', '#6*iGIjkKn', '2025-08-03 16:16:01', 'jodynorman@bryant-morrison.com', '+183038143', 'ESTJ', 'Science address their operation truth dark decide themselves.', NULL, NULL, NULL),
(35, 3021, 'lanefernando', 'kV%6PUxomK', '2025-08-03 16:16:01', 'jessica11@wright-reese.biz', '8109372974', 'INFJ', 'Customer paper direction in follow nearly within.', NULL, NULL, NULL),
(36, 3022, 'jordanjeffery', 'SaIs9Hr^%2', '2025-08-03 16:16:01', 'michaellittle@miller.org', '735.576.05', 'ISTP', 'By child different fill inside seem its yard style hot guess number vote.', NULL, NULL, NULL),
(37, 3023, 'colekathryn', 'oOEB2kUcf!', '2025-08-03 16:16:01', 'laurenfigueroa@yahoo.com', '0016752125', 'ISTJ', 'Purpose able miss success middle wish fire different matter life despite edge.', NULL, NULL, NULL),
(38, 3024, 'ryan52', 'Z2H1IaL2)j', '2025-08-03 16:16:01', 'chancock@nielsen-lucero.com', '1299369004', 'ENFP', 'Start into west he ready audience individual.', NULL, NULL, NULL),
(39, 3025, 'mrodriguez', 'k1ByJjaW*L', '2025-08-03 16:16:01', 'tnichols@contreras.com', '4751649625', 'ESFP', 'Scene must structure present option yourself talk real we after up.', NULL, NULL, NULL),
(40, 3026, 'robin05', 'Ei2TshJzu_', '2025-08-03 16:16:01', 'silvaadam@yahoo.com', '+159162841', 'ENTP', 'Board generation message boy method least forget second training democratic should nor yourself.', NULL, NULL, NULL),
(41, 3027, 'calebneal', 'O)@4Ju96d8', '2025-08-03 16:16:01', 'kking@hotmail.com', '0012625286', 'ESFJ', 'Station us administration PM send air career score add small use rock.', NULL, NULL, NULL),
(42, 3028, 'sheilalewis', 'MW7QoxFD^$', '2025-08-03 16:16:01', 'josephmatthews@yahoo.com', '9003913754', 'ENFP', 'Support conference improve color quality gun alone do.', NULL, NULL, NULL),
(43, 3029, 'yrodriguez', '$5ZgG7j6fN', '2025-08-03 16:16:01', 'ncook@gmail.com', '897.386.13', 'ENTJ', 'Kid remain again early indicate TV past begin analysis argue.', NULL, NULL, NULL),
(44, 3030, 'vanessa36', '#!Y6ZIKoei', '2025-08-03 16:16:01', 'nicholsonbrian@yahoo.com', '0013739795', 'ENFJ', 'In look strategy mean fund region drive much.', NULL, NULL, NULL),
(45, 3031, 'darryl04', '_HORIyd488', '2025-08-03 16:16:01', 'wadedennis@hotmail.com', '0011755415', 'ISTP', 'Way whom middle expert give themselves summer anything strong open employee.', NULL, NULL, NULL),
(46, 3032, 'klinejames', 't!xh6qUlP0', '2025-08-03 16:16:01', 'tannerlopez@collins.com', '0015992915', 'ESTP', 'Respond choose detail phone purpose role main hand small go sense.', NULL, NULL, NULL),
(47, 3033, 'welchjeanette', '1cyeNsVf@S', '2025-08-03 16:16:01', 'wendybrown@miller.com', '7462770381', 'ENTJ', 'Five present address choice simply behind agree dream decision party close.', NULL, NULL, NULL),
(48, 3034, 'kevindavis', 'W_qg1Dz7rQ', '2025-08-03 16:16:01', 'melissapatrick@yahoo.com', '2248526063', 'INTP', 'Election think current tough base organization natural property such fact.', NULL, NULL, NULL),
(49, 3035, 'barrjacob', 'O+D8aF*aOT', '2025-08-03 16:16:01', 'zlopez@smith.net', '0013624076', 'INTP', 'Security who firm sometimes although call tough film performance so company pass upon investment.', NULL, NULL, NULL),
(50, 3036, 'torresroger', 'Q()@8DcC!T', '2025-08-03 16:16:01', 'georgehubbard@hotmail.com', '592.578.07', 'INFJ', 'Goal PM American ago share several very coach create.', NULL, NULL, NULL),
(51, 3037, 'terri21', '2xx2&L+(w%', '2025-08-03 16:16:01', 'ymanning@gmail.com', '+129005376', 'ISFP', 'Start name material station high item along.', NULL, NULL, NULL),
(52, 3038, 'millerstephanie', '0hd6ML^k(5', '2025-08-03 16:16:01', 'wlynch@hotmail.com', '0018196115', 'ISFJ', 'Sea development water this hear year child political election do serious former law.', NULL, NULL, NULL),
(53, 3039, 'omckinney', 'jJ_O7jvW_6', '2025-08-03 16:16:01', 'tammyhenry@yahoo.com', '6574227510', 'INTJ', 'Husband green somebody treatment alone difficult gas partner our tend film indicate.', NULL, NULL, NULL),
(54, 3040, 'tammymoore', 'TF1OcUls_(', '2025-08-03 16:16:01', 'garzastefanie@hotmail.com', '0016857615', 'ENTP', 'Thank entire reduce mother task ask song last between herself then.', NULL, NULL, NULL),
(55, 3041, 'felicia33', '^34xMjuvUa', '2025-08-03 16:16:01', 'mcguiremelissa@lozano-little.org', '275.435.69', 'ESTJ', 'Former industry six one fact nice structure program stuff population sit.', NULL, NULL, NULL),
(56, 3042, 'wrightbrian', ')p2@JK)ye0', '2025-08-03 16:16:01', 'jeanettebrown@hotmail.com', '783.602.62', 'INFJ', 'Reduce service foot line reveal window.', NULL, NULL, NULL),
(57, 3043, 'stevensonmario', '@!1XWXZvUz', '2025-08-03 16:16:01', 'jonathan97@hotmail.com', '4229386796', 'INFJ', 'Event past school source along marriage beyond.', NULL, NULL, NULL),
(58, 3044, 'patriciamorgan', 'p_3Fx2#qeV', '2025-08-03 16:16:01', 'qmiller@gmail.com', '669.906.19', 'ESTJ', 'Official case street why information such participant like.', NULL, NULL, NULL),
(59, 3045, 'kristin62', '*MbuDQlrb7', '2025-08-03 16:16:01', 'megan84@gmail.com', '0010763014', 'INTP', 'Clear owner moment mission price article down without its indeed behavior item.', NULL, NULL, NULL),
(60, 3046, 'bergerdeborah', '(l4GAniq89', '2025-08-03 16:16:01', 'mirandaball@adams-smith.com', '182.057.47', 'ENFJ', 'Final wife knowledge late stay record involve ability.', NULL, NULL, NULL),
(61, 3047, 'sarah36', 'y*3CR+AF5f', '2025-08-03 16:16:01', 'allen01@arnold-lopez.net', '680.826.46', 'ESTP', 'Rest art without customer bad the exist range gas.', NULL, NULL, NULL),
(62, 3048, 'lopezmichelle', '_++B1s@l3M', '2025-08-03 16:16:01', 'kwarren@collins.com', '437.754.58', 'ISTP', 'Raise outside question office occur fact so single indicate become.', NULL, NULL, NULL),
(63, 3049, 'rkennedy', 'NV0B9WhB(M', '2025-08-03 16:16:01', 'acarter@gmail.com', '7531739546', 'ENTJ', 'Article heavy response grow race responsibility step lot maintain center foot accept.', NULL, NULL, NULL),
(64, 3050, 'stewartnathan', '^jvAl5zs^3', '2025-08-03 16:16:01', 'qthompson@gmail.com', '5105705694', 'ENFJ', 'Owner American effect citizen seven edge forget my this there enough.', NULL, NULL, NULL),
(65, 3051, 'miranda98', '29fY_0h&_B', '2025-08-03 16:16:01', 'kelsey10@carter.net', '+175497816', 'INTP', 'Husband upon of beyond feel require tonight vote team its pattern level data.', NULL, NULL, NULL),
(66, 3052, 'bhardin', '#dIjgl#8R5', '2025-08-03 16:16:01', 'jruiz@yahoo.com', '1655018667', 'ENTJ', 'During region he training food participant condition draw.', NULL, NULL, NULL),
(67, 3053, 'scoleman', 'Y6nGerQg&j', '2025-08-03 16:16:01', 'joan27@garcia.com', '9097070646', 'ENFJ', 'Protect true garden admit paper add project.', NULL, NULL, NULL),
(68, 3054, 'nphillips', 'w&x5jJdwMW', '2025-08-03 16:16:01', 'tsimpson@yahoo.com', '8017419302', 'ESTP', 'Debate above source sea ability involve site international receive lose.', NULL, NULL, NULL),
(69, 3055, 'clewis', 'IfED7Gu9m)', '2025-08-03 16:16:01', 'nmccoy@price-cline.info', '8692592998', 'ISFP', 'No who such work according decade sure.', NULL, NULL, NULL),
(70, 3056, 'smithbelinda', '&(U9Gqbx0I', '2025-08-03 16:16:01', 'nicole32@edwards.net', '872.757.32', 'ESFJ', 'Law score recently social old difference something conference investment bed early.', NULL, NULL, NULL),
(71, 3057, 'tara37', 'Ni9tPPuN_4', '2025-08-03 16:16:01', 'cookeric@hill.com', '5476308768', 'ESTJ', 'I father east rate prepare front.', NULL, NULL, NULL),
(72, 3058, 'rivascharles', '4!12oY4le_', '2025-08-03 16:16:01', 'elizabethmcbride@reyes.com', '3733227522', 'ESTP', 'Cut tell still program door from second traditional field war woman.', NULL, NULL, NULL),
(73, 3059, 'brownbrad', '@#N%3$Cv50', '2025-08-03 16:16:01', 'esanchez@armstrong-parker.org', '0912021867', 'INFJ', 'Me evidence thousand defense mouth view station produce try.', NULL, NULL, NULL),
(74, 3060, 'washingtoncynthia', '(pT4GlpDO(', '2025-08-03 16:16:01', 'richardwhite@hotmail.com', '2330515568', 'INTJ', 'Heavy catch hope increase picture there old consumer charge any wish.', NULL, NULL, NULL),
(75, 3061, 'marcia85', '2U1njBgBR$', '2025-08-03 16:16:01', 'michael21@carrillo.com', '0017075804', 'INFP', 'Treat score country test evidence early can level or figure area position.', NULL, NULL, NULL),
(76, 3062, 'rodriguezmeghan', 'bT(L7L+imW', '2025-08-03 16:16:01', 'daniel25@foster-rosario.com', '+180493923', 'ENFP', 'Save must Democrat give imagine clearly look attorney voice.', NULL, NULL, NULL),
(77, 3063, 'richard69', 'L()7Wief!O', '2025-08-03 16:16:01', 'priceanthony@hotmail.com', '167.598.12', 'INTJ', 'Newspaper trouble land us then likely station miss statement glass purpose beat until.', NULL, NULL, NULL),
(78, 3064, 'caseypatterson', 'I3Etv%1v+F', '2025-08-03 16:16:01', 'kirbyjessica@hotmail.com', '1516931789', 'ENTP', 'Energy tonight then improve dream cover team huge Democrat task national.', NULL, NULL, NULL),
(79, 3065, 'vstanley', 'HV7Di#wY_a', '2025-08-03 16:16:01', 'terrimurphy@gmail.com', '976.662.55', 'ISTP', 'Tax a happen physical shake politics dream.', NULL, NULL, NULL),
(80, 3066, 'rpowell', '%l+_y8Dv2w', '2025-08-03 16:16:01', 'qblankenship@christensen.com', '101.125.00', 'ISFJ', 'When because sing role quickly beautiful trial pick while.', NULL, NULL, NULL),
(81, 3067, 'brittany65', 'x%J0CIDkrg', '2025-08-03 16:16:01', 'walkerjohn@hotmail.com', '0017549042', 'ISFJ', 'Nor change site purpose avoid station to spend watch.', NULL, NULL, NULL),
(82, 3068, 'brianbrooks', 'Ou*k8Ernj5', '2025-08-03 16:16:01', 'renee51@gmail.com', '0396394607', 'INFP', 'Fine democratic onto sit plant five traditional painting way expect safe together clearly.', NULL, NULL, NULL),
(83, 3069, 'beckydavidson', 'M^X78Xy*+G', '2025-08-03 16:16:01', 'darryl07@yahoo.com', '0015714008', 'ENFJ', 'Product several subject help forward hundred support.', NULL, NULL, NULL),
(84, 3070, 'mark65', '*g3zZ%Tx2T', '2025-08-03 16:16:01', 'hmendoza@yahoo.com', '9754357877', 'ESTP', 'The other third fact high director.', NULL, NULL, NULL),
(85, 3071, 'mooreteresa', '%0OLX#HuIr', '2025-08-03 16:16:01', 'sullivanmary@hotmail.com', '324.918.72', 'ISTP', 'Maybe same five relationship wrong tend how remember despite blue Mrs to result.', NULL, NULL, NULL),
(86, 3072, 'pmiddleton', '@amVNivR4o', '2025-08-03 16:16:01', 'donald98@hotmail.com', '2257405763', 'INTP', 'Its edge ahead recent wait civil science.', NULL, NULL, NULL),
(87, 3073, 'grayjimmy', 'C!7%WTMnhy', '2025-08-03 16:16:01', 'hessteresa@cook-jackson.org', '8557117360', 'ENTJ', 'Likely put environmental major there employee air computer.', NULL, NULL, NULL),
(88, 3074, 'powellbruce', 'Q@89YJJhd0', '2025-08-03 16:16:01', 'cbryant@stephenson.biz', '879.649.92', 'ISFP', 'Three trial Republican popular offer should ability buy.', NULL, NULL, NULL),
(89, 3075, 'rogersstephen', ')O1Rh%yMDU', '2025-08-03 16:16:01', 'lorraine12@chavez-raymond.com', '690.968.31', 'ENTP', 'Way because reduce team sell social professional.', NULL, NULL, NULL),
(90, 3076, 'wgreene', '#jX5jA(ey4', '2025-08-03 16:16:01', 'clopez@gmail.com', '102.640.84', 'ESFJ', 'Hair task exactly others then alone write adult between work name argue.', NULL, NULL, NULL),
(91, 3077, 'steven60', 'b1_JV!ZD$h', '2025-08-03 16:16:01', 'juarezwilliam@hotmail.com', '0645558628', 'ISFJ', 'Hundred recently western music majority what book four field clear cover team.', NULL, NULL, NULL),
(92, 3078, 'oreynolds', '^lqgJE&j(1', '2025-08-03 16:16:01', 'peter51@macias.com', '+191537577', 'ENTJ', 'Notice dinner arrive fall according bit pattern she public effort prove budget.', NULL, NULL, NULL),
(93, 3079, 'jacobsnyder', '4231ATLh$R', '2025-08-03 16:16:01', 'kenneth62@gmail.com', '0013958206', 'ESFJ', 'Sort challenge happen option land since source sea threat however religious.', NULL, NULL, NULL),
(94, 3080, 'markfernandez', '4NQqd6Os#7', '2025-08-03 16:16:01', 'patricia44@bradley.com', '8413965176', 'ENTP', 'Trade system top impact sell before western her financial.', NULL, NULL, NULL),
(95, 3081, 'nbriggs', '5W+9NPIhCd', '2025-08-03 16:16:01', 'nicholas45@blanchard.net', '+164949693', 'INFJ', 'Practice enjoy second cause positive radio Democrat single beautiful black operation form yeah.', NULL, NULL, NULL),
(96, 3082, 'michael18', 'Z05j1^Rd@U', '2025-08-03 16:16:01', 'kevin03@gmail.com', '1715091707', 'ISFP', 'Involve president wish employee certain check performance relationship learn news sure when.', NULL, NULL, NULL),
(97, 3083, 'sarah57', 'n2*EFYLt%T', '2025-08-03 16:16:01', 'ehill@hotmail.com', '730.027.32', 'ESFJ', 'Save star learn have stuff everybody direction key discuss.', NULL, NULL, NULL),
(98, 3084, 'jacobgonzalez', '^ov14PaVbz', '2025-08-03 16:16:01', 'cody42@gmail.com', '6123536639', 'ESTJ', 'Cover wonder every threat take start democratic artist.', NULL, NULL, NULL),
(99, 3085, 'ronald86', 'PN5Vgm9#v)', '2025-08-03 16:16:01', 'carlsparks@gmail.com', '970.163.63', 'ENFP', 'Chair everything land hand everybody really fund hope increase be chair design.', NULL, NULL, NULL),
(100, 3086, 'bellbrooke', '^8kK1X9^zE', '2025-08-03 16:16:01', 'brian43@hunt-daniels.com', '0013160729', 'ESFP', 'Against last lot office treatment share high three sit tree.', NULL, NULL, NULL),
(101, 3087, 'trodriguez', '%V%ThWOe7z', '2025-08-03 16:16:01', 'karaburnett@yahoo.com', '0018289330', 'ESTP', 'Resource seek our sometimes nice country grow step my create.', NULL, NULL, NULL),
(102, 3088, 'olawrence', 's+3UO5oov6', '2025-08-03 16:16:01', 'raymond05@torres.info', '0013451985', 'ENTJ', 'Late yet main move deal measure teach sound medical evidence middle.', NULL, NULL, NULL),
(103, 3089, 'qramos', 'S6P7Vp4l#G', '2025-08-03 16:16:01', 'priscilla66@peck-joseph.com', '4684316705', 'ENTP', 'Event catch mother nothing beat court these so issue culture.', NULL, NULL, NULL),
(104, 3090, 'dustin39', 'eCD%ItMJ^6', '2025-08-03 16:16:01', 'willie33@bryan.org', '472.554.34', 'INTP', 'Husband during especially yeah so manager blood.', NULL, NULL, NULL),
(105, 3091, 'riveracraig', 'nzVx1F7j@Z', '2025-08-03 16:16:01', 'tarnold@hotmail.com', '7268268071', 'ESFJ', 'Marriage trial would report but heavy third history this push goal half.', NULL, NULL, NULL),
(106, 3092, 'martinezkatherine', '(v7APA&q(0', '2025-08-03 16:16:01', 'qcohen@yahoo.com', '4795760841', 'INFP', 'At know mention within eye can develop growth involve soldier offer get involve.', NULL, NULL, NULL),
(107, 3093, 'april79', 'g%S9IhKt#K', '2025-08-03 16:16:01', 'mcneilamy@harris.com', '0012533026', 'ESTJ', 'Instead specific yes person tough save.', NULL, NULL, NULL),
(108, 3094, 'dylanallen', 'p!6S_8Or6a', '2025-08-12 20:04:11', 'rhonda84@gmail.com', '2175980110', 'INTP', 'Administration food feeling close forward teacher computer generation institution leader.', NULL, NULL, 'music'),
(109, 3095, 'fernandezjacob', 'z#9Ud_uoAF', '2025-08-03 16:16:01', 'janet87@yahoo.com', '+116529012', 'ESFJ', 'Short machine do public old tree common court whom concern build.', NULL, NULL, NULL),
(110, 3096, 'francotaylor', '+9ZcgNtc+k', '2025-08-03 16:16:01', 'mistyzimmerman@webster-hurley.com', '4448609716', 'ISFP', 'Side traditional store fill kid meet produce.', NULL, NULL, NULL),
(111, 3097, 'rochadennis', 'I*3yGGYH1o', '2025-08-03 16:16:01', 'egilbert@weeks-palmer.com', '+116279266', 'ISFP', 'Particular perhaps management read report mission memory on.', NULL, NULL, NULL),
(112, 3098, 'kenneth64', 'q*5H$jP6&1', '2025-08-03 16:16:01', 'sjacobs@yahoo.com', '269.466.65', 'INTJ', 'Movie international poor song day open modern wind enough floor.', NULL, NULL, NULL),
(113, 3099, 'anitakim', 'Q_B8SjLs&G', '2025-08-03 16:16:01', 'wreid@yahoo.com', '547.524.32', 'INFP', 'Push no industry only politics throw inside listen seat coach yard continue claim.', NULL, NULL, NULL),
(114, 3100, 'michael38', 'X!2GfUZlYa', '2025-08-03 16:16:01', 'ghatfield@hotmail.com', '6476785934', 'ISFP', 'Usually happen drug big rest choice.', NULL, NULL, NULL),
(115, 971221, 'ADMIN', '1', '2025-08-12 23:04:54', 'admin@email.com', '', 'ENTJ', '', 'uploads/profile_971221_1755028188.jpg', NULL, 'tourism');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `date` (`date`),
  ADD KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
