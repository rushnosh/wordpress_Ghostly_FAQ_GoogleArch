
//Main Class to import
class FaqToggle {
    //Initial set up of the class
    constructor(){
        this.itemGroup = document.querySelectorAll('.faq-item')
        this.assignToggles()
    }
    //Events - use this area to handle event calls
    assignToggles() {
        this.itemGroup.forEach((parentEl) => {
            parentEl.querySelector('.faq-question').addEventListener('click', (e) => this.ourClickDispatcher(e))
          });
    }

    //Methods - use this area to apply logic in your code
    ourClickDispatcher(e) {
        let currentQuestion = e.target
        if (currentQuestion.getAttribute("data-clicked") == "Yes") {
            this.noToggleAnswer(currentQuestion)
        } else {
            this.yesToggleAnswer(currentQuestion)
        }

    }

    yesToggleAnswer(cq) {
        //Remove any previous answer displays
        let currentOpen = document.querySelector(`.faq-question[data-clicked='Yes']`)
        if (currentOpen) {     
            currentOpen.setAttribute("data-clicked", "No")   
            currentOpen.nextElementSibling.classList.remove("faq-a-card__animate");  
            currentOpen.nextElementSibling.style.display = "none"
        }
        
        //Show the answer
        cq.setAttribute("data-clicked", "Yes")
        cq.nextElementSibling.classList.add("faq-a-card__animate");
        cq.nextElementSibling.style.display = "block"
    }

    noToggleAnswer(cq) {
        cq.setAttribute("data-clicked", "No")
        cq.nextElementSibling.classList.remove("faq-a-card__animate");
        cq.nextElementSibling.style.display = "none"
    }
}
//Required to export the class to the js index file
export default FaqToggle;