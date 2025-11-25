<?php
/**
 * Home Controller
 * Handles the landing page
 */
class HomeController extends Controller {
    
    public function index(): void {
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        } else {
            $this->redirect('login');
        }
    }
}
