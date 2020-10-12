$(document).ready(function(){
    $("input[type='radio']").click(function(){
        var radioValue = $("input[name='method']:checked").val();
        if(radioValue == "svm") {
            $("#h2id").show();
            $("#kmeansform").hide();
            $("#bayesform").hide();
            $("#mpnsform").hide();
            $("#svmform").show();
        } else if(radioValue == "kmeans") {
            $("#h2id").show();
            $("#bayesform").hide();
            $("#mpnsform").hide();
            $("#svmform").hide();
            $("#kmeansform").show();
        } else if(radioValue == "bayes") {
            $("#h2id").show();
            $("#mpnsform").hide();
            $("#svmform").hide();
            $("#kmeansform").hide();
            $("#bayesform").show();
        } else if(radioValue == "mpns") {
            $("#h2id").show();
            $("#svmform").hide();
            $("#kmeansform").hide();
            $("#bayesform").hide();
            $("#mpnsform").show();
        }
    });
});