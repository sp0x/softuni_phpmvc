var app = angular.module('app', []).config(function($interpolateProvider){
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
});
app.controller('xCartGeneralController', function($scope, $compile, $templateCache, $http) {

    var self = $scope;
    $scope.productQuantity = 1;

    $scope.routes = {
        cart : {
            add : "/cart/add",
            remove : "/cart/remove"
        }
    };
    $scope.cart = {
        items : []
    };

    $scope.checkoutCart = function(url){
        var data = "";
        var itemIds = Object.keys(self.cart.items);
        for(var i=0; i<itemIds.length; i++){
            var itemId = itemIds[i];
            var item = self.cart.items[itemId];
            var chunk = itemId + ":" + item.quantity;
            if(i<(itemIds.length-1)) chunk += ",";
            data += chunk;
        }
        window.location = url + "?buff=" + data;
    };

    $scope.addCartProduct = function(product){
        var price = product.cost;
        var id = product.id;
        self.cart.items[id] = product;
        self.cart.items[id].addQuantity = function(){             this.quantity++;        };
        self.cart.items[id].removeQuantity = function(){ if(this.quantity>0) this.quantity--; };
        self.cart.items[id].getTotalCost = function(){
            return (this.quantity * this.cost).toFixed(2);
        };
    };
    $scope.getProductsCostTotal = function(){
        var total = 0.0;
        var cartKeys = Object.keys(self.cart.items);
        for(var i=0; i<cartKeys.length; i++){
            var cartKey = cartKeys[i];
            var cartItem = self.cart.items[cartKey];
            total += parseFloat(cartItem.getTotalCost());
        }
        return (total).toFixed(2);
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