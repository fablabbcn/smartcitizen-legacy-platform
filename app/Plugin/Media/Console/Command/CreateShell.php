<?php
class CreateShell extends AppShell {
    public function main() {
    	$this->hr();
        $this->out('Creation des tables MEDIAS');
        $this->hr();
        if (!config('database')) {
			$this->out(__d('cake_console', 'Your database configuration was not found. Take a moment to create one.'), true);
			return $this->DbConfig->execute();
		}else{
			$this->dispatchShell('schema create --plugin Media');
			return true;
		}
    }
}