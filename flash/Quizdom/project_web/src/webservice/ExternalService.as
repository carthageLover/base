package webservice 
{
	//import flash.events.EventDispatcher;
	import flash.external.ExternalInterface;
	/**
	 * @author marco
	 */
	public class ExternalService //extends EventDispatcher 
	{
		// reference callbacks
		private var _payCallback:Function;
		private var _massCallback:Function;
		
		public function ExternalService() { }
		
		public function callPayFBUI(item_id:String, payCallback:Function) : String 
		{
			_payCallback = payCallback;
			ExternalInterface.addCallback("payBack", callPayCallback);
			
			trace("Calling buy JS function...");
			return ExternalInterface.call("buy", item_id);
		}
		
		private function callPayCallback(str:String):void 
		{
			// validate purchase //
			
			var data:Object = JSON.parse(str);
			var response:String = "";
			var success:Boolean = false;
			
			if (data['order_id']) {
			  // Facebook only returns an order_id if you've implemented
			  // the Credits Callback payments_status_update and settled
			  // the user's placed order.
			  success = true;
			  // Notify the user that the purchased item has been delivered
			  // without a complete reload of the game.
			  response=
						"<b>Transaction Completed!</b> <br><br>"
						+ "Data returned from Facebook: <br>"
						+ "Order ID: " + data['order_id'] + "<br>"
						+ "Status: " + data['status'];
			} else if (data['error_code']) {
			  // Appropriately alert the user.
			  response=
						"<b>Transaction Failed!</b> <br><br>"
						+ "Error message returned from Facebook:<br>"
						+ data['error_code'] + " - "
						+ data['error_message'];
			} else {
			  // Appropriately alert the user.
			  response="<b>Transaction failed!</b>";
			}			
				
			_payCallback(success,response);
			// remove the callback
			ExternalInterface.addCallback("payBack", null); 
			_payCallback = null;
		}
		
		public function callInviteFBUI(item_id:String=null) : String 
		{	
			trace("Calling Invite JS function...");
			return ExternalInterface.call("invite");
		}
		
		public function callMassiveSendFBUI(type:int, friends:String, massCallback:Function) : void 
		{	
			_massCallback = massCallback;
			ExternalInterface.addCallback("massCallback", callMassCallback);
			
			if (type == 0) // invites
				ExternalInterface.call("massiveInvite", friends);
			else if (type == 1) // lives, accept
				ExternalInterface.call("sendLife", friends, "accept");
			else if (type == 2) // lives, ask for help
				ExternalInterface.call("sendLife", friends, "help");
		}
		
		private function callMassCallback(data:String):void 
		{
			_massCallback(data);
			// remove the callback
			ExternalInterface.addCallback("massCallback", null); 
			_massCallback = null;
		}
		
	}

}