var app = angular.module('app', []);
app.controller('xCartGeneralController', function($scope, $compile, $templateCache, $http) {
    var self = $scope;
    $scope.productQuantity = 1;

    $scope.routes = {
        cart : {
            add : "/cart/add",
            remove : "/cart/remove"
        }
    };
    $scope.setCartRoutes = function(routeTable){
        $scope.routes.cart = routeTable;
    };
    $scope.AddProductToCart = function(productId){
        //add the project to the cart
        var url = self.routes.cart.add;
        var data = "id=" + productId;
        $http({
            method: 'POST',
            url: url, //Update or add a new auth
            data: data, // pass in data as strings
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' } // set the headers so angular passing info as form data (not request payload)
        })
            .success(function (data) {
                if(typeof data == "string") data = JSON.parse(data);
                if(data.success){
                    //All is well
                    alert("The product was added to your cart!");
                }else{
                    alert("Error: " + data.message);
                }
            });
    }
});