var Askit = function () {

    function _toggleQuestionBindEvent(element){
        element.observe('click', __toggleQuestionEvent);
    }
    function __toggleQuestionEvent(event){
        Event.stop(event);
        this.next().toggle();
    }
    function _showNewQuestionFormEvent(event){
        this.up().hide();
        this.up().next().show();
    }
    function _hideNewQuestionFormEvent(event){
        this.up(3).hide();
        this.up(3).previous().show();
    }
    function _showNewAnswerForm(element){
        element.observe('click', __showNewAnswerFormEvent);
    }
    function __showNewAnswerFormEvent(event){
        this.up().hide();
        this.up().next().show();
    }
    function _hideNewAnswerForm(element){
        element.observe('click', __hideNewAnswerFormEvent);
    }
    function __hideNewAnswerFormEvent(event){
        this.up(3).hide();
        this.up(3).previous().show();
    }

    return {
        init: function(){
            $$('.askit-add-answer-button').each(_showNewAnswerForm);
            $$('.askit-add-answer-h5').each(_hideNewAnswerForm);
            $$('.askit-accordion-toggle').each(_toggleQuestionBindEvent);

            $$('.askit-accordion-content').invoke('hide')

            if (null != $('askit-add-question-button')) {
                $('askit-add-question-button').observe(
                    'click', _showNewQuestionFormEvent
                );
            }
            if (null != $('askit-add-question-h5')) {
                $('askit-add-question-h5').observe(
                    'click', _hideNewQuestionFormEvent
                );
            }

        }
    }
};
//onready
document.observe("dom:loaded", function(){
    Askit().init();
});