import json
import sched
import time
from datetime import datetime


def load_tasks(file_path="tasks.json"):
    with open(file_path, "r", encoding="utf-8") as f:
        return json.load(f)


def schedule_tasks(tasks):
    scheduler = sched.scheduler(time.time, time.sleep)

    for task in tasks:
        delay = task.get("delay", 0)
        name = task.get("task", "Unnamed task")

        def task_action(task_name=name):
            print(f"{datetime.now().isoformat(timespec='seconds')} - Executing: {task_name}")

        scheduler.enter(delay, 1, task_action)

    print("Starting task scheduler. Press Ctrl+C to exit.")
    try:
        scheduler.run()
    except KeyboardInterrupt:
        print("Scheduler stopped.")


def main():
    tasks = load_tasks()
    schedule_tasks(tasks)


if __name__ == "__main__":
    main()
