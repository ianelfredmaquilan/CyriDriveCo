
public class MyOrders {
	public class Synchronization {

	}
	class Counter {
	    private int count = 0;

	    
	    public synchronized void increment() {
	        count++;
	    }

	    public int getCount() {
	        return count;
	    }
	}

	class MyOrder extends Thread {
	    Counter counter;

	    MyOrder(Counter counter) {
	        this.counter = counter;
	    }

	    public void run() {
	        for(int i = 0; i < 1 ; i++) {
	            counter.increment();
	        }
	    }


	    public static void main(String[] args) throws InterruptedException {

	        Counter counter = new counter();

	        MyOrder o1 = new MyOrder(counter);
	        MyOrder o2 = new MyOrder(counter);
	        MyOrder o3 = new MyOrder(counter);

	        o1.start();
	        o2.start();
	        o3.start();
	   
	        o1.join();
	        o2.join();
	        o3.join();

	        System.out.println("Final Orders: " + counter.getCount());
	    }
	    
	}
}


