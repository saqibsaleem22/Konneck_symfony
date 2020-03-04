PETSHELTER
It’s a web application on animal adoption. Animal shelters and dog pounds can upload their animals data for users to adopt them, and in conclussion for letting users see elegible pets for adoption.

Members of group
NAME	UNIVERSITY MAIL	GITHUB ACCOUNT
Arshia Ambar Saleem	aa.saleem.2017@alumnos.urjc.es	ArshiaSaleem98
Borja Castro Cruces	b.castro.2018@alumnos.urjc.es	borja123456
Marina Fernández Suárez	m.fernandezsu@alumnos.urjc.es	IhoFenixMFS
Rodrigo Cadena Rodríguez	r.cadenar.2019@alumnos.urjc.es	CadenaR
Used Tools
Trello
Link to our board.

Entities:
User:
Registered users can request the adoption of any animal that the search shows them. In addition, they can upload images to their profiles.

Attributes:
Photo.
Name.
DNI.
Adress.
House size (Big/Medium/Little).
Garden (No/Little/Medium/Big).
Children number.
People living in the house.
E-mail.
Password.
Galery.
Animals:
The available animals list would be visible, here will appear an image of the animal, his/her name, the age, the owner and if it has been already adopted or not.

Attributes:
Photo.
Name.
Age.
Animal type (Dog/Cat/Bird/Reptile/Equine).
Size (XL/Big/Medium/Little).
Description.
Owner.
Status (Adopted or not).
Shelter:
The shelter can upload animals for them to get adopted, and accept or deny user adoption requests.

Attributes:
Name.
NIF.
E-mail.
Password.
Average Rating.
Description.
Address.
Animal list.
Adoption Requests Received (Animal/User).
Adoptions:
In order to have a register of the adoptions made by the users an adption table is needed.

Attributes:
Date.
Animal ID.
User ID.
Users permissions:
Anonimous
Anonymously or through any user type it is possible to access Home, the animal list Animals and the animal profile, the shelter or users profile, and Contact Us.

User
This user type can request adoptions, edit his/her profile data, and upload images to his/her gallery.

Shelter
This user type can edit its profile data, accept or deny its adoption requests, and upload new animals to the platform.

Images:
Registered users and animals have their profile image which can be edited and updated by them or their shelter in case that is an animal profile. In addition, users have an image gallery associated in which they can upload all the pictures they want.

Graphs:
The adoption statistics are going to be shown through a diagram in the graph view.

Complementary Technology:
When a user requests an adoption, an e-mail will be sent to the proprietary shelter.

Advanced Query or Algorithm:
The shown animal list will filter available animals and change depending on the house size of the user navigating through the web. For example, a big dog wouldn’t appear in the list of a small house owner.

Screenshots
