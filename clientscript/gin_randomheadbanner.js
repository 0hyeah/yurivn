function randomImages(){
	var maxAdNo = 13
	var adNo
	var myAd = new Array()

	myAd[0] = "http://yurivn.net/styles/K-ON!/header_top2_couple1.jpg"
	myAd[1] = "http://yurivn.net/styles/K-ON!/header_top2_couple2.jpg"
        myAd[2] = "http://yurivn.net/styles/K-ON!/header_top2_couple3.jpg"
        myAd[3] = "http://yurivn.net/styles/K-ON!/header_top2_couple4.jpg"
        myAd[4] = "http://yurivn.net/styles/K-ON!/header_top2_couple5.jpg"
        myAd[5] = "http://yurivn.net/styles/K-ON!/header_top2_couple6.jpg"
        myAd[6] = "http://yurivn.net/styles/K-ON!/header_top2_couple7.jpg"
        myAd[7] = "http://yurivn.net/styles/K-ON!/header_top2_couple8.jpg"
        myAd[8] = "http://yurivn.net/styles/K-ON!/header_top2_couple9.jpg"
        myAd[9] = "http://yurivn.net/styles/K-ON!/header_top2_couple10.jpg"
        myAd[10] = "http://yurivn.net/styles/K-ON!/header_top2_couple11.jpg"
        myAd[11] = "http://yurivn.net/styles/K-ON!/header_top2_couple12.jpg"
        myAd[12] = "http://yurivn.net/styles/K-ON!/header_top2_couple13.jpg"
        myAd[13] = "http://yurivn.net/styles/K-ON!/header_top2_couple15.jpg"

	adNo = Math.round(Math.random() * maxAdNo)
                
	var top = document.getElementById("top"); 
	var bottom = document.getElementById("bottom");
	top.src = myAd[adNo];
                
	if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple1.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple1.jpg"
	else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple2.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple2.jpg"
        else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple3.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple3.jpg"
        else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple4.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple4.jpg"
        else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple5.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple5.jpg"
        else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple6.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple6.jpg"
        else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple7.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple7.jpg"
        else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple8.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple8.jpg"
        else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple9.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple9.jpg"
        else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple10.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple10.jpg"
        else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple11.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple11.jpg"
        else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple12.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple12.jpg"
        else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple13.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple13.jpg"
	else if(top.src == "http://yurivn.net/styles/K-ON!/header_top2_couple15.jpg")
		bottom.src = "http://yurivn.net/styles/K-ON!/login_right_couple15.jpg"
}