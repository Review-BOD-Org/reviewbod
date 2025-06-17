<style>
   /* Add these styles to your existing CSS */
/* Styling for issues based on date type */
.issue-item.created-date {
    border-left: 3px solid #8b5cf6; /* Purple for created date */
}

.created-date-label {
    background-color: #8b5cf6;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
    margin-left: 5px;
}

.due-date-label {
    background-color: #3b82f6;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
    margin-left: 5px;
}

/* Keep priority coloring as well */
.issue-item.priority-1 {
    border-left: 3px solid #ef4444; /* Red for Urgent */
}

.issue-item.priority-2 {
    border-left: 3px solid #f59e0b; /* Amber for High */
}

.issue-item.priority-3 {
    border-left: 3px solid #3b82f6; /* Blue for Medium */
}

.issue-item.priority-4 {
    border-left: 3px solid #10b981; /* Green for Low */
}
/* Calendar enhancements */
.calendar-container {
    max-width: 1200px;
    padding: 20px;
}

.months-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 40px;
}

/* For small screens, show one month per row */
@media (max-width: 768px) {
    .months-grid {
        grid-template-columns: 1fr;
    }
}

/* Day styling for calendar */
td {
    position: relative;
    cursor: pointer;
    transition: background-color 0.2s;
}

td:hover {
    background-color: #f5f5f5;
}

/* Issues indicator */
.has-issues {
    background-color: #e6f7ff;
    font-weight: bold;
    color: #0066cc;
}

.issue-count {
    position: absolute;
    top: 2px;
    right: 2px;
    background-color: #0066cc;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Issues panel */
.issues-panel {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    width: 80%;
    max-width: 500px;
    max-height: 80vh;
    overflow-y: auto;
    z-index: 1000;
    padding: 20px;
    display: none;
}

.issues-panel.visible {
    display: block;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.panel-header h3 {
    margin: 0;
    font-size: 18px;
}

.close-panel {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
}

.issues-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.issue-item {
    border: 1px solid #eee;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 10px;
}

.issue-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.issue-id {
    font-weight: bold;
    color: #666;
}

.issue-priority {
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 4px;
}

.issue-title {
    font-weight: bold;
    margin-bottom: 5px;
}

.issue-state {
    font-size: 13px;
    color: #666;
    margin-bottom: 10px;
}

.issue-link {
    display: inline-block;
    font-size: 13px;
    color: #0066cc;
    text-decoration: none;
}

/* Priority colors */
.priority-1 {
    border-left: 4px solid #ff0000;
}

.priority-2 {
    border-left: 4px solid #ff9900;
}

.priority-3 {
    border-left: 4px solid #ffcc00;
}

.priority-4 {
    border-left: 4px solid #cccccc;
}
    .calendar-container {
        display: flex;
        flex-direction: column;
 
        margin: 0 auto;
    }
    
    .year-navigation {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .year-title {
        font-size: 24px;
        font-weight: bold;
        margin-right: 15px;
    }
    
    ._nav_button{
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        margin: 0 5px;
        color: #333;
    }
    
    .months-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .month-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .month-header {
        text-align: center;
        padding: 15px;
        font-weight: bold;
        font-size: 18px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th {
        padding: 10px;
        text-align: center;
        font-weight: 500;
        font-size: 14px;
    }
    
    td {
        padding: 12px;
        text-align: center;
        border: 1px solid #f0f0f0;
        font-size: 14px;
    }
    
    .other-month {
        color: #ccc;
        background-color: #f9f9f9;
    }
    
    .vertical-line {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 1px;
        background-color: #f0d0d0;
        z-index: -1;
    }
    
    .vertical-lines {
        position: relative;
        width: 100%;
        height: 100%;
    }
    
    /* Positions for vertical lines - approximated from the reference */
    .line-1 { left: 20%; }
    .line-2 { left: 40%; }
    .line-3 { left: 60%; }
    .line-4 { left: 80%; }
</style>
 
<div class="vertical-lines">
    <div class="vertical-line line-1"></div>
    <div class="vertical-line line-2"></div>
    <div class="vertical-line line-3"></div>
    <div class="vertical-line line-4"></div>
</div>

<div class="calendar-container">
    <div class="year-navigation">
        <h1 class="year-title">2025</h1>
        <button class="nav-button _nav_button" id="left-nav">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
        <button class="nav-button _nav_button" id="right-nav">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>
    </div>
    
    <div class="months-grid">
        <!-- January -->
        <div class="month-card">
            <div class="month-header">Jan</div>
            <table>
                <thead>
                    <tr>
                        <th>Mo</th>
                        <th>Tu</th>
                        <th>We</th>
                        <th>Th</th>
                        <th>Fr</th>
                        <th>Sa</th>
                        <th>Su</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="other-month">29</td>
                        <td class="other-month">30</td>
                        <td class="other-month">31</td>
                        <td>1</td>
                        <td>2</td>
                        <td>3</td>
                        <td>4</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>6</td>
                        <td>7</td>
                        <td>8</td>
                        <td>9</td>
                        <td>10</td>
                        <td>11</td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td>13</td>
                        <td>14</td>
                        <td>15</td>
                        <td>16</td>
                        <td>17</td>
                        <td>18</td>
                    </tr>
                    <tr>
                        <td>19</td>
                        <td>20</td>
                        <td>21</td>
                        <td>22</td>
                        <td>23</td>
                        <td>24</td>
                        <td>25</td>
                    </tr>
                    <tr>
                        <td>26</td>
                        <td>27</td>
                        <td>28</td>
                        <td>29</td>
                        <td>30</td>
                        <td class="other-month">1</td>
                        <td class="other-month">2</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- February -->
        <div class="month-card">
            <div class="month-header">Feb</div>
            <table>
                <thead>
                    <tr>
                        <th>Mo</th>
                        <th>Tu</th>
                        <th>We</th>
                        <th>Th</th>
                        <th>Fr</th>
                        <th>Sa</th>
                        <th>Su</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="other-month">29</td>
                        <td class="other-month">30</td>
                        <td class="other-month">31</td>
                        <td>1</td>
                        <td>2</td>
                        <td>3</td>
                        <td>4</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>6</td>
                        <td>7</td>
                        <td>8</td>
                        <td>9</td>
                        <td>10</td>
                        <td>11</td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td>13</td>
                        <td>14</td>
                        <td>15</td>
                        <td>16</td>
                        <td>17</td>
                        <td>18</td>
                    </tr>
                    <tr>
                        <td>19</td>
                        <td>20</td>
                        <td>21</td>
                        <td>22</td>
                        <td>23</td>
                        <td>24</td>
                        <td>25</td>
                    </tr>
                    <tr>
                        <td>26</td>
                        <td>27</td>
                        <td>28</td>
                        <td>29</td>
                        <td>30</td>
                        <td class="other-month">1</td>
                        <td class="other-month">2</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- March -->
        <div class="month-card">
            <div class="month-header">Feb</div>
            <table>
                <thead>
                    <tr>
                        <th>Mo</th>
                        <th>Tu</th>
                        <th>We</th>
                        <th>Th</th>
                        <th>Fr</th>
                        <th>Sa</th>
                        <th>Su</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="other-month">29</td>
                        <td class="other-month">30</td>
                        <td class="other-month">31</td>
                        <td>1</td>
                        <td>2</td>
                        <td>3</td>
                        <td>4</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>6</td>
                        <td>7</td>
                        <td>8</td>
                        <td>9</td>
                        <td>10</td>
                        <td>11</td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td>13</td>
                        <td>14</td>
                        <td>15</td>
                        <td>16</td>
                        <td>17</td>
                        <td>18</td>
                    </tr>
                    <tr>
                        <td>19</td>
                        <td>20</td>
                        <td>21</td>
                        <td>22</td>
                        <td>23</td>
                        <td>24</td>
                        <td>25</td>
                    </tr>
                    <tr>
                        <td>26</td>
                        <td>27</td>
                        <td>28</td>
                        <td>29</td>
                        <td>30</td>
                        <td class="other-month">1</td>
                        <td class="other-month">2</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

