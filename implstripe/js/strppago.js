//CLAVE PUBLICA DE STRIPE
//const stripe = Stripe("pk_test_51HBvroG7NsTelwXIVVtr7YrL5uZYRpej68vNMHPhN7KHrL7TJROFyXvVwOpqTHXXTC9RsSXLxNkgqEoWaOMU117E00JLSkcIkw");

// The items the customer wants to buy

let elements;
var formcorrecto=false;

//primera peticion a stripe
async function initialize() {
	

	//guardo el formulario
	const data = new FormData(document.getElementById('estudiosFrm'));
	
    const { clientSecret } = await fetch("./implstripe/creapaymentintent.php", {
	    method: "POST",
	    body: JSON.stringify(Object.fromEntries( data)),
 	 }).then((r) => r.json());
	elements = stripe.elements({ clientSecret });
	
	
	
	  const paymentElementOptions = {
	   
	 fields: {
	            billingDetails: {
	                address: {
	                    country: 'never'
	                }
	            }
	        },
		  };
	
	  const paymentElement = elements.create("payment", paymentElementOptions);
	  paymentElement.mount("#payment-element");
///reviso que no haya errores en el formulario de pago
	paymentElement.on('change', function(event) {
	  if (event.complete) {
	    // puedo dar click
		formcorrecto=true;

	  }
	});
}



async function handleSubmit(e) {
		
	e.preventDefault();
    setLoading(true);
	//creo la cita primero
	//guardo el formulario
	var idcita=0;
	
	try{
		const data = new FormData(document.getElementById('estudiosFrm'));
		idcita  = await fetch("./implstripe/creacita.php", {
	    method: "POST",
	    body: JSON.stringify(Object.fromEntries( data)),
			}).then((r) => r.json());
	}
	catch(err)
	{
		console.log(err);
		alert("Hubo un error");
		return;
		}
	//exit();
// console.log("---"+idcita)  ;
	
	if(idcita>0) //ahora si proceso el pago
	{

	  const { error } = await stripe.confirmPayment({
	    elements,
	    confirmParams: {
	    	
	      // Make sure to change this to your payment completion page
	      return_url: "http://localhost/nuevoOID/citaStripe/respuestapagos.php",
 payment_method_data: {
                billing_details: {
                    address: {
                        country: 'MX'
                    }

                }
            },
	    },
	  });


	  // This point will only be reached if there is an immediate error when
	  // confirming the payment. Otherwise, your customer will be redirected to
	  // your `return_url`. For some payment methods like iDEAL, your customer will
	  // be redirected to an intermediate site first to authorize the payment, then
	  // redirected to the `return_url`.
	  if (error.type === "card_error"){
		  cancelarCita(idcita);
		//console.log("hubo un error cancelar cita"+error.type);
	    showMessage(error.message);
	  } else if(error.type === "validation_error") {
		  cancelarCita(idcita);
		//console.log("hubo un error cancelar cita"+error.type);
	    showMessage(error.message);
	  } else {
		cancelarCita(idcita);
		//  console.log(error.type);
	    showMessage("Error inesperado intente de nuevo.");
	  }
	}
	else
	{
		alert("Hubo un error");
	}
	

	  setLoading(false);
}


// Fetches the payment intent status after payment submission
async function checkStatus() {
  const clientSecret = new URLSearchParams(window.location.search).get(
    "payment_intent_client_secret"
  );

  if (!clientSecret) {
    return;
  }

  const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

  switch (paymentIntent.status) {
    case "succeeded":
      showMessage("Payment succeeded!");
      break;
    case "processing":
      showMessage("Your payment is processing.");
      break;
    case "requires_payment_method":
      showMessage("Your payment was not successful, please try again.");
      break;
    default:
      showMessage("Something went wrong.");
      break;
  }
}

// ------- UI helpers -------

	function showMessage(messageText) {
		alert(messageText);
	 /* const messageContainer = document.querySelector("#payment-message");
	
	  messageContainer.classList.remove("hidden");
	  messageContainer.textContent = messageText;
	
	  setTimeout(function () {
	    messageContainer.classList.add("hidden");
	    messageContainer.textContent = "";
	  }, 8000);*/
	}

// Show a spinner on payment submission
	function setLoading(isLoading) {
	  if (isLoading) {
	    // Disable the button and show a spinner
	    document.querySelector("#btnPagar").disabled = true;
	    document.querySelector("#spinner").classList.remove("hidden");
	  //  document.querySelector("#button-text").classList.add("hidden");
	  } else {
	    document.querySelector("#btnPagar").disabled = false;
	    document.querySelector("#spinner").classList.add("hidden");
	  //  document.querySelector("#button-text").classList.remove("hidden");
	  }
	}

	async function cancelarCita(vidcita){
		
			const formData = new FormData();
			 formData.append('idcita', vidcita);
			idcita  = await fetch("./implstripe/cancelacita.php", {
		    method: "POST",
		  
	    body: formData,
				}).then((r) => r.json());
	}