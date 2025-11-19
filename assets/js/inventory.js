// Google Sheets Integration for Plot Inventory
// Vrinda Green City - Real Estate Inventory Management

(function($) {
    'use strict';

    const SHEET_ID = '1nXWp1nzgx3Lx5zuDm3Efp32w6YOkh7xx2-yMcMaYBgg';
    const SHEET_NAME = 'Sheet1'; // Change if your sheet has a different name
    const SHEET_URL = `https://docs.google.com/spreadsheets/d/${SHEET_ID}/gviz/tq?tqx=out:csv&sheet=${SHEET_NAME}`;
    
    let plotsData = [];
    let currentFilters = {
        status: 'all',
        direction: '',
        area: ''
    };
    
    $(document).ready(function() {
        loadInventoryData();
        setupFilters();
    });
    
    function loadInventoryData() {
        $('#inventoryLoading').show();
        $('#inventoryGrid').empty();
        $('#emptyState').hide();
        
        $.ajax({
            url: SHEET_URL,
            dataType: 'text',
            success: function(csvData) {
                console.log('CSV Data received successfully');
                parseCSVData(csvData);
                populateFilterOptions();
                displayPlots();
                $('#inventoryLoading').hide();
            },
            error: function(error) {
                console.error('Error loading inventory:', error);
                $('#inventoryLoading').hide();
                showError();
            }
        });
    }
    
    function parseCSVData(csvText) {
        const lines = csvText.split('\n');
        plotsData = [];
        
        // Parse header to understand column order
        const headerValues = parseCSVLine(lines[0]);
        console.log('CSV Headers:', headerValues);
        
        // Find column indices
        const colIndex = {
            plotNo: headerValues.findIndex(h => h.toLowerCase().includes('plot')),
            dimension: headerValues.findIndex(h => h.toLowerCase().includes('dimension')),
            area: headerValues.findIndex(h => h.toLowerCase().includes('area')),
            direction: headerValues.findIndex(h => h.toLowerCase().includes('direction')),
            status: headerValues.findIndex(h => h.toLowerCase().includes('status'))
        };
        
        console.log('Column Mapping:', colIndex);
        
        // Parse data rows (start from index 1)
        for (let i = 1; i < lines.length; i++) {
            if (!lines[i].trim()) continue;
            
            // Split by comma, but handle quotes
            const values = parseCSVLine(lines[i]);
            
            if (values.length >= 5) {
                const plot = {
                    plotNo: values[colIndex.plotNo] || values[0],
                    dimension: values[colIndex.dimension] || values[1],
                    area: values[colIndex.area] || values[2],
                    direction: values[colIndex.direction] || values[3],
                    status: (values[colIndex.status] || values[4]).trim()
                };
                
                plotsData.push(plot);
                
                // Log first plot for debugging
                if (i === 1) {
                    console.log('First Plot Sample:', plot);
                }
            }
        }
        
        console.log('Parsed plots:', plotsData.length);
    }
    
    function parseCSVLine(line) {
        const result = [];
        let current = '';
        let inQuotes = false;
        
        for (let i = 0; i < line.length; i++) {
            const char = line[i];
            
            if (char === '"') {
                inQuotes = !inQuotes;
            } else if (char === ',' && !inQuotes) {
                result.push(current.trim().replace(/^"|"$/g, ''));
                current = '';
            } else {
                current += char;
            }
        }
        
        result.push(current.trim().replace(/^"|"$/g, ''));
        return result;
    }
    
    function populateFilterOptions() {
        // Get unique directions
        const directions = [...new Set(plotsData.map(plot => plot.direction))].sort();
        const directionSelect = $('#directionFilter');
        directions.forEach(direction => {
            if (direction) {
                directionSelect.append(`<option value="${direction}">${direction}</option>`);
            }
        });
        
        // Area options will be populated based on selected direction
        // So we don't populate all areas initially
    }
    
    function updateAreaOptions(selectedDirection) {
        const areaSelect = $('#areaFilter');
        
        // Clear existing options except the first placeholder
        areaSelect.find('option:not(:first)').remove();
        
        if (!selectedDirection) {
            // Reset area filter if no direction selected
            currentFilters.area = '';
            return;
        }
        
        // Get unique areas for the selected direction
        const availableAreas = [...new Set(
            plotsData
                .filter(plot => plot.direction === selectedDirection)
                .map(plot => plot.area)
        )].sort();
        
        // Populate area dropdown with filtered areas
        availableAreas.forEach(area => {
            if (area) {
                areaSelect.append(`<option value="${area}">${area}</option>`);
            }
        });
        
        // Reset area selection
        areaSelect.prop('selectedIndex', 0);
        currentFilters.area = '';
    }
    
    function displayPlots() {
        const $grid = $('#inventoryGrid');
        $grid.empty();
        
        let filteredPlots = plotsData.filter(plot => {
            // Filter by status
            if (currentFilters.status !== 'all' && 
                plot.status.toLowerCase() !== currentFilters.status.toLowerCase()) {
                return false;
            }
            
            // Filter by direction (required - no "all" option)
            if (!currentFilters.direction || currentFilters.direction === '') {
                return false; // Don't show plots if no direction selected
            }
            if (plot.direction !== currentFilters.direction) {
                return false;
            }
            
            // Filter by area (required - no "all" option)
            if (!currentFilters.area || currentFilters.area === '') {
                return false; // Don't show plots if no area selected
            }
            if (plot.area !== currentFilters.area) {
                return false;
            }
            
            return true;
        });
        
        // Update result count
        updateResultCount(filteredPlots.length, plotsData.length);
        
        if (filteredPlots.length === 0) {
            $('#emptyState').show();
            return;
        }
        
        $('#emptyState').hide();
        
        filteredPlots.forEach((plot, index) => {
            const plotCard = createPlotCard(plot, index);
            $grid.append(plotCard);
        });
        
        // Reinitialize WOW.js for new elements
        if (typeof WOW !== 'undefined') {
            new WOW().init();
        }
    }
    
    function updateResultCount(filtered, total) {
        const countText = filtered === total 
            ? `Showing all ${total} plots` 
            : `Showing ${filtered} of ${total} plots`;
        $('#resultCount').text(countText);
    }
    
    function createPlotCard(plot, index) {
        const statusClass = `status-${plot.status.toLowerCase().replace(/\s+/g, '-')}`;
        const isAvailable = plot.status.toLowerCase() === 'available';
        const delay = (index % 3) * 0.1 + 0.2;
        
        return `
            <div class="col-lg-4 col-md-6">
                <div class="plot-card wow fadeInUp" data-wow-delay="${delay}s">
                    <div class="plot-header">
                        <div class="plot-number">Plot #${plot.plotNo}</div>
                        <span class="plot-status ${statusClass}">${plot.status}</span>
                    </div>
                    
                    <div class="plot-details">
                        <div class="plot-detail-row">
                            <span class="plot-detail-label">Dimension</span>
                            <span class="plot-detail-value">${plot.dimension}</span>
                        </div>
                        <div class="plot-detail-row">
                            <span class="plot-detail-label">Area</span>
                            <span class="plot-detail-value">${plot.area}</span>
                        </div>
                        <div class="plot-detail-row">
                            <span class="plot-detail-label">Direction</span>
                            <span class="plot-detail-value plot-direction">
                                <span class="direction-icon">
                                    <i class="fas fa-compass"></i>
                                </span>
                                ${plot.direction}
                            </span>
                        </div>
                    </div>
                    
                    <div class="plot-actions">
                        ${isAvailable ? 
                            `<button class="plot-btn btn-inquire" onclick="inquirePlot('${plot.plotNo}')">
                                <i class="fas fa-phone"></i> Inquire Now
                            </button>` :
                            `<button class="plot-btn btn-booked" disabled>
                                <i class="fas fa-check"></i> ${plot.status}
                            </button>`
                        }
                    </div>
                </div>
            </div>
        `;
    }
    
    function setupFilters() {
        // Direction filter - updates area options when changed
        $('#directionFilter').on('change', function() {
            currentFilters.direction = $(this).val();
            updateAreaOptions(currentFilters.direction);
            displayPlots();
        });
        
        // Area filter
        $('#areaFilter').on('change', function() {
            currentFilters.area = $(this).val();
            displayPlots();
        });
        
        // Status filter
        $('#statusFilter').on('change', function() {
            currentFilters.status = $(this).val();
            displayPlots();
        });
        
        // Reset filters button
        $('#resetFilters').on('click', function() {
            currentFilters = {
                status: 'all',
                direction: '',
                area: ''
            };
            $('#statusFilter').val('all');
            $('#directionFilter').prop('selectedIndex', 0);
            $('#areaFilter').prop('selectedIndex', 0);
            
            // Clear area options
            $('#areaFilter').find('option:not(:first)').remove();
            
            displayPlots();
        });
    }
    
    function showError() {
        $('#inventoryGrid').html(`
            <div class="col-12 text-center">
                <div class="alert" style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 40px; margin-bottom: 15px;"></i>
                    <h5>Unable to load inventory data</h5>
                    <p>Please make sure the Google Sheet is publicly accessible and try again later.</p>
                    <button onclick="location.reload()" class="btn" style="background: #0D9B4D; color: #fff; margin-top: 10px;">
                        <i class="fas fa-redo"></i> Retry
                    </button>
                </div>
            </div>
        `);
    }
    
    // Global function for inquire button
    window.inquirePlot = function(plotNo) {
        // Redirect to contact page with plot information
        window.location.href = `contact.html?plot=${plotNo}`;
    };
    
})(jQuery);
